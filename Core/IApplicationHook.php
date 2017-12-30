<?php
namespace Sepbin\System\Core;

interface IApplicationHook
{
	
	public function applicationStart( Application $app ) :void;
	
	public function applicationEnd( Application $app ):void;
	
	public function applicationException( int $errno, string $errstr, string $errfile, int $errline ):void;
	
	public function applicationWarning( int $errno, string $errstr, string $errfile, int $errline ):void;
	
}