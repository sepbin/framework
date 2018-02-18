<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class O_INLUCDE
{
	
	static public function parse( \Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $filename ){
		
		if(empty($filename)) return '';
		
		$fullname = $this->manager->styleDir.'/'.$filename;
		$fullname = trim($fullname);
		
		return "\n<!--include $filename-->\n".SyntaxUtil::phpTag('$this->manage->includeContent( $this, '.$this->getVarOrStr($fullname).' )')."\n\n<!--include end-->\n";
		
	}
	
}