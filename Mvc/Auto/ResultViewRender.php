<?php
namespace Sepbin\System\Mvc\Auto;


use Sepbin\System\Mvc\ViewRender;
use Sepbin\System\Mvc\Model;


class ResultViewRender extends ViewRender
{
	
	
	public function get( Model $model ) {
		
		return $this->getModelData($model);
		
	}
	
	
}