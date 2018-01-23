<?php
namespace Sepbin\System\Frame\Restful;


use Sepbin\System\Mvc\Model;
use Sepbin\System\Mvc\ViewRender;

class RestfulViewRender extends ViewRender
{
	
	
	public function get( Model $model ){
		
		return $this->getModelData($model);
		
	}
	
	
}