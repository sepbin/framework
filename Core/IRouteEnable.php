<?php
namespace Sepbin\System\Core;

use Sepbin\System\Util\IFactoryEnable;

interface IRouteEnable extends IFactoryEnable
{
		
	public function RouteMapper( array $params );
	
}