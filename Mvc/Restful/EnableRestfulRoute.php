<?php
namespace Sepbin\System\Mvc\Restful;

use Sepbin\System\Mvc\AbsRoute;
use Sepbin\System\Mvc\AbsController;

class EnableRestfulRout extends AbsRoute
{
	
	public function findController( string $default ):string{
		
		return $default;
		
	}
	
	public function findAction( string $default, AbsController $controller ):string{
		
		return $default;
		
	}
	
}