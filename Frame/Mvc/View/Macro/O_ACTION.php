<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class O_ACTION
{
	
	static public function parse( \Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $controller, $action, ...$params ){
		
		$str = '$this->manage->includeController(';
		
		$str.= SyntaxUtil::macroVar($controller).',';
		$str.= SyntaxUtil::macroVar($action);
		
		if(!empty($params)){
			$params = array_map(function($val){
				return SyntaxUtil::macroVar($val);
			}, $params);
				$params = implode(',', $params);
				$params = ','.$params;
				
				$str.=$params;
		}
		
		$str.= ')';
		
		return SyntaxUtil::phpTag( $str );
		
	}
	
}