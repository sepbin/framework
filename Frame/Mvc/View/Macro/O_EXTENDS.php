<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class O_EXTENDS
{
	
	static public function parse( \Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $module, $controller, $action, ...$params ){
		
		$module = ucfirst($module);
		$controller = ucfirst($controller);
		
		$params = array_map( function($val){
			
			return SyntaxUtil::macroVar($val);
			
		} , $params);
		
		
		return SyntaxUtil::phpTag('
			
			$this->manage->isParent = true;
			$this->manage->parentModule = \''.$module.'\';
			$this->manage->parentController = \''.$controller.'\';
			$this->manage->parentAction = \''.$action.'\';
			$this->manage->parentParams = [ '.implode(',', $params).' ];
			$this->manage->parentFilename = \''.$module.'/'.$controller.'/'.$action.'\';
			
		');
		
		
	}
	
}