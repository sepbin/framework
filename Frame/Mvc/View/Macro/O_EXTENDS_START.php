<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class O_EXTENDS_START
{
	
	static public function parse(\Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $key){
		
		return SyntaxUtil::phpTag(' 
			
			if( !$this->manage->ignoreParent ){
				ob_start(function($content){ 
					$this->manage->putExtendContent(\''.$key.'\',$content); return "error:'.$key.'"; 
				});
			}

		');
		
	}
	
}