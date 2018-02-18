<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class O_CONTENT
{
	
	static public function parse(\Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $key){
		
// 		return SyntaxUtil::phpTag(' $this->manage->includeContent($this) ');
		
		return SyntaxUtil::phpTag(' echo $this->manage->getExtendContent(\''.$key.'\') ');
		
	}
	
}