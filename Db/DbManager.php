<?php
namespace Sepbin\System\Db;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Db\Driver\IDriver;
use Sepbin\System\Util\Factory;
use Sepbin\System\Db\Exception\SqlException;
use Sepbin\System\Util\ArrayUtil;
use Sepbin\System\Db\Sql\StandardSql;

class DbManager extends Base implements IFactoryEnable
{
	
	/**
	 * 读驱动和默认的写驱动
	 * @var IDriver
	 */
	private $driver;
	
	/**
	 * 写驱动
	 * @var IDriver
	 */
	private $writeDriver;
	
	private $configNamespace;
	
	private $lastCommand;
    
	
	private $sqlHelper;
	
	/**
	 * 表名前缀
	 * @var string
	 */
	private $prefix='';
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):DbManager{
		
		return Factory::get(DbManager::class,$config_namespace,$config_file,$config_path);
		
	}
	
	public function _init(\Sepbin\System\Util\FactoryConfig $config){
		
		$this->configNamespace = $config->getNamespace();
		$driver = $config->getStr('driver','Sepbin\System\Db\Driver\Mysql');
		$writeDriver = $config->getBool('write_driver',false);
		$this->prefix = $config->getStr('prefix');
		$this->sqlHelper = $config->getStr('sql', StandardSql::class);
		
		$this->driver = $config->getInstance('driver', $driver, IDriver::class );
		
		if( $writeDriver ){
			$this->writeDriver = $config->getInstance('write_driver', $driver );
		}
		
	}
	
	public function prepare( $sql, ...$params ){
		
		foreach ($params as $key=>$val){
		    if(!is_numeric($val)){
		        $val = str_replace("'", "''", $val);
		        $val = "'$val'";
		    }
			
			$params[$key] = $val;
		}
		
		return sprintf($sql, ...$params);
		
	}
	
	
	/**
	 * 执行插入
	 * @param string $sql
	 * @return unknown|boolean
	 */
	public function insert( string $sql ){
		
		$result = $this->exec($sql);
		
		if( $result !== false ){
		    $lastId = $this->driver->getLastInsertId();
		    if($lastId) return $lastId;
		    return $result;
		}
		
		return false;
		
	}
	
	/**
	 * 执行更新
	 * @param string $sql
	 * @return unknown
	 */
	public function update( string $sql){
		
		return $this->exec($sql);
		
	}
	
	
	
	/**
	 * 执行删除
	 * @param string $sql
	 * @return mixed
	 */
	public function delete( string $sql ){
	    
        return $this->exec($sql);
	    
	}
	
	
	/**
	 * 获取记录
	 * @param string $sql
	 * @return array[]
	 */
	public function read( string $sql ){
	    
	    $this->lastCommand = $sql;
		
		return $this->driver->query($sql);
		
	}
	
	
	/**
	 * 获取一行
	 * @param string $sql
	 * @return array
	 */
	public function first( string $sql ){
		
		$result = $this->read($sql);
		if( $result ){
			return $result[0];
		}
		
		return $result;
		
	}
	
	
	
	/**
	 * 获取一个
	 * @param string $sql
	 */
	public function var( string $sql ){
	    $result = $this->first($sql);
	    if(!empty($result)) return current($result);
	    return '';
	}
	
	
	
	/**
	 * 获取一列
	 * @param string $sql
	 * @param string $col
	 * @param bool $unique
	 * @return array|unknown
	 */
	public function col( string $sql, string $col='', bool $unique=false ){
		
		$result = $this->read($sql);
		if( $result ){
			if($col == '') $col = key($result[0]);
			return ArrayUtil::getArrayCol($result, $col, $unique);
		}
		return $result;
		
	}
	
	
	
	/**
	 * 关闭连接
	 */
	public function close(){
		$this->driver->close();
		Factory::destroy( get_class($this), $this->configNamespace );
	}
	
	
	/**
	 * 开始事务
	 */
	public function beginTrans(){
	    $this->driver->beginTrans();
	}
	
	
	/**
	 * 回滚事务
	 */
	public function rollBack(){
	    $this->driver->rollBackTrans();
	}
	
	
	
	/**
	 * 提交事务
	 */
	public function commit(){
	    $this->driver->commitTrans();
	}
	
	
	/*
	 * 执行事务
	 */
	public function trans( \Closure $process ){
		
		$this->beginTrans();
		
		try {
    		if( $process( $this ) === false ){
    			$this->rollBack();
    			return false;
    		}
		}catch ( \Exception $e ){
		    
		    $this->rollBack();
		    
		    throw $e;
		    
		}catch ( \Error $e ){
		    
		    $this->rollBack();
		    
		    throw $e;
		    
		}
		
		$this->commit();
		return true;
		
	}
	
	
	
	
	/**
	 * 执行SQL，并返回结果
	 * @param string $sql
	 * @return mixed
	 */
	public function exec( string $sql ){
	    
	    $this->lastCommand = $sql;
	    
	    if( $this->writeDriver == null ){
	        $result = $this->driver->exec($sql);
	    }else{
	        $result = $this->writeDriver->exec($sql);
	    }
	    
		if( getApp()->isDebug() && $result === false ){
		    if( $this->writeDriver == null ) $err = $this->driver->getError();
		    else $err = $this->writeDriver->getError();
			throw (new SqlException())->appendMsg( $err .'['.$this->getLastCommand().']' );
		}
		
		return $result;
		
	}
	
	
	
	
	/**
	 * 获取sql助手
	 * @param string $table
	 * @return \Sepbin\System\Db\AbsSql
	 */
	public function getSQL( string $table ){
	    
	    return (new $this->sqlHelper($table))->pre($this->prefix)->setManager($this);
	    
	}
	
	
	
	/**
	 * 获取sql的where生成器
	 * @return \Sepbin\System\Db\SqlWhere
	 */
	public function getWhere(){
	    
	    return (new SqlWhere())->pre($this->prefix);
	    
	}
	
	
	
	
	
	/**
	 * 获取最后执行的指令
	 * @return \Sepbin\System\Db\string
	 */
	public function getLastCommand(){
	    return $this->lastCommand;
	}
    
	
	
}