<?php
namespace Sepbin\System\Mvc\Route;

use Sepbin\System\Mvc\AbsController;

class RuleRoute extends RwriteRoute
{
	
	
	public function findController( string $default ):string{
		
		return $default;
		
	}
	
	
	public function findAction( string $default, AbsController $controller ): string{
		
		return $default;
		
	}
	
}