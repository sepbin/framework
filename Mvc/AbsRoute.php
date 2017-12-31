<?php
namespace Sepbin\System\Mvc;

use Sepbin\System\Core\Base;

abstract class AbsRoute extends Base
{
	
	
	abstract public function findController( string $default ):string;
	
	abstract public function findAction( string $default, AbsController $controller ):string;
	
	
}