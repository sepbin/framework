<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

class D_VIEWPATH
{
	
	static public function parse( \Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $file='' ){
		
		return $manage->stylePath.'/'.$file;
		
	}
	
}