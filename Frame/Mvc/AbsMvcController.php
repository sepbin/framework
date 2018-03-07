<?php
namespace Sepbin\System\Frame\Mvc;

use Sepbin\System\Frame\AbsController;
use Sepbin\System\Frame\Hook\IMvcTemplateHook;

abstract class AbsMvcController extends AbsController implements IMvcTemplateHook
{
	
    
	
	public function _start(){
		
		getApp()->registerHook(IMvcTemplateHook::class, $this);
		
		parent::_start();
		
	}
	
	
	/**
	 *
	 * @param \Sepbin\System\Frame\Mvc\View\TemplateManager $manager
	 */
	public function tplManagerInit( \Sepbin\System\Frame\Mvc\View\TemplateManager $manager ){
		
	}
	
	/**
	 *
	 * @param string $content
	 * @return string
	 */
	public function tplCacheBefore( string $content ) : string{
		
		return $content;
		
	}
	
	
	public function tplViewBefore( string $content ) : string{
		
		
		return $content;
		
	}
	
	
	
}