<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class O_EXTENDS_SET
{
	
	static public function parse( \Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $key, $value ){
		
		
		return SyntaxUtil::phpTag('
			
			$this->manage->extendContent[\''.$key.'\'] = \''.$value.'\';
			
		');
		
		
	}
	
}