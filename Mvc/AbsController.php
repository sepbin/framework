<?php
namespace Sepbin\System\Mvc;

use Sepbin\System\Core\Base;


abstract class AbsController extends Base
{
	
	
	function __construct(){
		
	}
	
	/**
	 * 
	 * @return Model
	 */
	protected function createModel():Model{
		
		return new Model();
		
	}
	
	
}