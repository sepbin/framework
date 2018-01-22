<?php
namespace Sepbin\System\Core\Hook;

interface IApplicationHook
{
	
	
	public function applicationStart( \Sepbin\System\Core\Application $app ) :void;
	
	public function applicationEnd( \Sepbin\System\Core\Application $app ):void;
	
	public function applicationException( int $errno, string $errstr, string $errfile, int $errline ):void;
	
	public function applicationWarning( int $errno, string $errstr, string $errfile, int $errline ):void;
	
}