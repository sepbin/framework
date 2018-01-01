<?php

namespace Sepbin\System\Mvc\Restful;

use Sepbin\System\Mvc\AbsController;
use Sepbin\System\Mvc\Model;


abstract class AbsRestfulController extends AbsController
{
	
	
	/**
	 * 
	 * @return RestfulModel
	 */
	protected function createModel():Model{
		
		return new RestfulModel();
		
	}
	
	
}