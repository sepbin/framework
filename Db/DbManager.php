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
		$this->driver = new $driver;
		
		$this->driver->connect($config->getStr('host'), 
				$config->getStr('database'), 
				$config->getStr('user'), 
				$config->getStr('pass'),
				$config->getInt('port',0));
		
	}
	
	public function prepare( $sql, ...$params ){
		
		if( !get_magic_quotes_gpc() ){
			foreach ($params as $key=>$val){
				$params[$key] = addslashes($val);
			}
		}
		
		return sprintf($sql, ...$params);
		
	}
	
	public function create( string $sql ){
		
		$result = $this->exec($sql);
		
		
		if( $result !== false ){
			return $this->driver->getLastInsertId();
		}
		
		return false;
		
	}
	
	public function update( string $sql){
		
		return $this->exec($sql);
		
	}
	
	
	public function read( string $sql ){
		
		return $this->driver->query($sql);
		
	}
	
	
	public function first( string $sql ){
		
		$result = $this->read($sql);
		if( $result ){
			return $result[0];
		}
		
		return $result;
		
	}
	
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