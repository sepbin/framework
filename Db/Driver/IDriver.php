<?php
namespace Sepbin\System\Db\Driver;

use Sepbin\System\Util\IFactoryEnable;

interface IDriver extends IFactoryEnable{
	
	
	public function exec( string $sql );
	
	public function query( string $sql );
	
	public function getError();
	
	public function getLastInsertId();
	
	public function beginTrans();
	
	public function commitTrans();
	
	public function rollBackTrans();
	
	public function close();
	
}