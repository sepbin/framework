<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;

use Sepbin\System\Frame\Mvc\View\SyntaxUtil;

class O_EXTENDS_END
{
	
	static public function parse(\Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $key=''){
		
		if( $key == '' ){
			return SyntaxUtil::phpTag(' if(!$this->manage->ignoreParent) ob_end_clean() ');
		}
		
		return 
		SyntaxUtil::phpTag(' 
				if(!$this->manage->ignoreParent){
					ob_end_clean();
					echo $this->manage->getExtendContent(\''.$key.'\');
				}
		');
		
	}
	
}