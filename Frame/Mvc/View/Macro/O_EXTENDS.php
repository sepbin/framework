<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class O_EXTENDS
{
	
	static public function parse( \Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $filename ){
		
		
// 		$manage->isParent = true;
// 		$manage->parentFilename = $filename;
		
		return SyntaxUtil::phpTag('
			
			$this->manage->isParent = true;
			$this->manage->parentFilename = \''.$filename.'\';
			
		');
		
		
	}
	
}