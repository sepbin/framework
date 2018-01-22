<?php
namespace Sepbin\System\Db\Driver;

use Sepbin\System\Util\Factory;

class Sqlite3 implements IDriver
{
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):Sqlite3{
		
		return Factory::get(Sqlite3::class,$config_namespace,$config_file,$config_path);
		
	}
	
	public function _init(\Sepbin\System\Util\FactoryConfig $config){
		
		
		
	}
	
	
	public function exec( string $sql ){
		
	}
	
	
	public function query( string $sql ){
		
	}
	
	
	public function getError(){
		
	}
	
	
	public function getLastInsertId(){
		
	}
	
	
	public function beginTrans(){
		
	}
	
	
	public function commitTrans(){
		
	}
	
	
	public function rollBackTrans(){
		
	}
	
	
	public function close(){
		
	}
	
	
}