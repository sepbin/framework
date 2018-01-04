<?php
namespace Sepbin\System\Db\Driver;

class Mysql implements IDriver
{
	
	/**
	 * pdo对象
	 * @var \PDO
	 */
	private $pdo;
	
	public function connect( string $host, string $dbname, string $user, string $pass, int $port=0, bool $pconnect=false ){
		
		if($port == 0) $port = 3306;
		
		if( $pconnect ){
			$this->pdo = new \PDO("mysql:host=$host;dbname=$dbname;$port",$user,$pass,array(
				\PDO::ATTR_PERSISTENT => true
			));
		}else{
			
			$this->pdo = new \PDO("mysql:host=$host;dbname=$dbname;$port",$user,$pass);
		}
		
	}
	
	
	
	public function query( string $sql ){
		
		return $this->pdo->query($sql)->fetchAll( \PDO::FETCH_ASSOC );
		
		
	}
	
	public function exec( string $sql ){
		
		return $this->pdo->exec($sql);
		
	}
	
	public function getError(){
		
		return $this->pdo->errorInfo();
		
	}
	
	public function getLastInsertId(){
		
		return $this->pdo->lastInsertId();
		
	}
	
	public function beginTrans(){
		
		$this->pdo->beginTransaction();
		
	}
	
	public function commitTrans(){
		
		$this->pdo->commit();
		
	}
	
	public function rollBackTrans(){
		
		$this->pdo->rollBack();
		
	}
	
	
	public function close(){
		$this->pdo = null;
	}
	
}