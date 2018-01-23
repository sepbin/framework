<?php
namespace Sepbin\System\Frame\Mvc\View\Macro;


class D_URL
{
	
	static public function parse( \Sepbin\System\Frame\Mvc\View\TemplateManager $manage, $url='' ){
		
		if( $url != '' ){
			$url = '/'.$url;
		}
		
		if( getApp()->httpRewrite ){
			return HTTP_ROOT.$url;
		}
		
		return HTTP_ROOT.'/index.php'.$url;
		
	}
	
}