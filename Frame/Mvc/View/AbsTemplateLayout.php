<?php
namespace Sepbin\System\Frame\Mvc\View;

use Sepbin\System\Core\Base;
use Sepbin\System\Frame\Model;

abstract class AbsTemplateLayout extends Base
{
	
	protected $manage;
	
	function __construct( TemplateManager $templateManager ){
		
		$this->manage = $templateManager;
		
	}
	
	abstract public function action():Model;
	
}