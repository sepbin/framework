<?php
namespace Sepbin\System\Db;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Db\Driver\IDriver;
use Sepbin\System\Util\Factory;
use Sepbin\System\Db\Exception\SqlException;
use Sepbin\System\Util\ArrayUtil;

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
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):DbManager{
		
		return Factory::get(DbManager::class,$config_namespace,$config_file,$config_path);
		
	}
	
	public function _init(\Sepbin\System\Util\FactoryConfig $config){
		
		$this->configNamespace = $config->getNamespace();
		$driver = $config->getStr('driver','Sepbin\System\Db\Driver\Mysql');
		$writeDriver = $config->getBool('write_driver',false);
		
		$this->driver = $config->getInstance('driver', $driver );
		if( $writeDriver ){
			$this->writeDriver = $config->getInstance('write_driver', $driver );
		}
		
	}
	
	public function prepare( $sql, ...$params ){
		
		if( !get_magic_quotes_gpc() ){
			foreach ($params as $key=>$val){
				$params[$key] = addslashes($val);
			}
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
			return $this->driver->getLastInsertId();
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
	 * 获取记录
	 * @param string $sql
	 * @return array[]
	 */
	public function read( string $sql ){
		
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
	
	
	public function delete( string $sql ){
		
		return $this->driver->exec($sql);
		
	}
	
	public function close(){
		$this->driver->close();
		Factory::destroy( get_class($this), $this->configNamespace );
	}
	
	
	/*
	 * 执行事务
	 */
	public function trans( \Closure $process ){
		
		$this->driver->beginTrans();
		
		if( $process() === false ){
			$this->driver->rollBackTrans();
			return false;
		}
		
		$this->driver->commitTrans();
		return true;
		
	}
	
	
	private function exec( string $sql ){
		$result = $this->driver->exec($sql);
		
		if( getApp()->isDebug() && $result === false ){
			throw (new SqlException())->appendMsg( $this->driver->getError()[2] );
		}
		
		return $result;
		
	}
	
	
}