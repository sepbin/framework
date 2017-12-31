<?php
namespace Sepbin\System\Mvc;

class Render
{
	
	
	public function get( Model $model ) : string{
		
		return '';
		
	}
	
	
	/**
	 * 按照model所声明的自动渲染类型进行渲染
	 * @param Model $model
	 * @return string
	 */
	protected function autoRender( Model $model ) : string {
		
		return '';
		
	}
	
}