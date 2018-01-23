<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class O_EXTENDS_END
{
	
	static public function parse(\Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $key=''){
		
		if( $key == '' ){
			return SyntaxUtil::phpTag(' ob_end_clean() ');
		}
		return SyntaxUtil::phpTag(' ob_end_clean() ') . SyntaxUtil::phpTag(' echo $this->manage->getExtendContent(\''.$key.'\') ');
		
	}
	
}