<?php

namespace Sepbin\System\Mvc\Restful;

use Sepbin\System\Mvc\AbsController;
use Sepbin\System\Mvc\Model;
use Sepbin\System\Http\Response;


abstract class AbsRestfulController extends AbsController
{
	
	
	protected $autoDataType = Response::DATA_TYPE_JSON;
	
	
	function __destruct(){
		
		getApp()->getResponse()->setContentType( $this->autoDataType );
		
	}
	
	
	/**
	 * 
	 * @return RestfulModel
	 */
	protected function createModel():Model{
		
		return new RestfulModel();
		
	}
	
	
}