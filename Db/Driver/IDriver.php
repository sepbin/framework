<?php
namespace Sepbin\System\Db\Driver;

interface IDriver{
	
	
	public function connect( string $host, string $dbname, string $user, string $pass, int $port=0, bool $pconnect=false );
	
	public function exec( string $sql );
	
	public function query( string $sql );
	
	public function getError();
	
	public function getLastInsertId();
	
	public function beginTrans();
	
	public function commitTrans();
	
	public function rollBackTrans();
	
	public function close();
	
}