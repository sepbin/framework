<?php
namespace Sepbin\System\Db\Driver;

use Sepbin\System\Util\Factory;
use Sepbin\System\Db\Exception\ConnectException;

class Sqlite3 implements IDriver
{
    
    private $driver;
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):Sqlite3{
		
		return Factory::get(Sqlite3::class,$config_namespace,$config_file,$config_path);
		
	}
	
	public function _init(\Sepbin\System\Util\FactoryConfig $config){
	    
	    try {
	        $this->driver = new \SQLite3( DOCUMENT_ROOT.$config->getStr('database') );
	    }catch (\Exception $e){
	        throw (new ConnectException())->appendMsg( 'sqlite3:'. DOCUMENT_ROOT.$config->getStr('database') );
	    }
		
	}
	
	
	public function exec( string $sql ){
		
	    return $this->driver->exec( $sql );
	    
	}
	
	
	public function query( string $sql ){
		
	    $result = $this->driver->query($sql) ;
	    
	    if( $result instanceof \SQLite3Result ){
	        
	        $data = array();
	        
	        while ( false != ($item = $result->fetchArray( SQLITE3_ASSOC ) ) ){
	            $data[] = $item;
	        }
	        
	        return $data;
	        
	    }
	    
	    return array();
	    
	}
	
	
	public function getError(){
		
	    return $this->driver->lastErrorMsg();
	    
	}
	
	
	public function getLastInsertId(){
		
	    return $this->driver->lastInsertRowID();
	    
	}
	
	
	public function beginTrans(){
		
	    return $this->driver->exec('BEGIN');
	    
	}
	
	
	public function commitTrans(){
		
	    return $this->driver->exec('COMMIT');
	    
	}
	
	
	public function rollBackTrans(){
		
	    return $this->driver->exec('ROLLBACK');
	    
	}
	
	
	public function close(){
		
	    return $this->driver->close();
	    
	}
	
	
}