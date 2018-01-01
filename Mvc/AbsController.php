<?php
namespace Sepbin\System\Mvc;

use Sepbin\System\Core\Base;


abstract class AbsController extends Base
{
	
	
	/**
	 * 
	 * @return Model
	 */
	protected function createModel():Model{
		
		return new Model();
		
	}
	
	
}