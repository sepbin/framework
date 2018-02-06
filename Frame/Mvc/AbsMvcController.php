<?php
namespace Sepbin\System\Frame\Mvc;

use Sepbin\System\Frame\AbsController;
use Sepbin\System\Core\Request;
use Sepbin\System\Frame\Hook\IMvcTemplateHook;
use Sepbin\System\Http\Response;
use Sepbin\System\Frame\Model;
use Sepbin\System\Frame\Mvc\View\TemplateObject;

abstract class AbsMvcController extends AbsController implements IMvcTemplateHook
{
	
	public $layoutModel;
	
	
	public function _start(){
		
		if( getApp()->getRequest()->getRequestType() == Request::REQUEST_TYPE_BROSWER ){
			getApp()->getResponse()->setContentType( Response::DATA_TYPE_HTML );
		}else{
			getApp()->getResponse()->setContentType( getApp()->defaultDataFormat );
		}
		getApp()->registerHook(IMvcTemplateHook::class, $this);
		
		$this->layoutModel = new Model();
		
		parent::_start();
		
	}
	
	
	/**
	 *
	 * @param \Sepbin\System\Frame\Mvc\View\TemplateManager $manager
	 */
	public function tplManagerInit( \Sepbin\System\Frame\Mvc\View\TemplateManager $manager ) : void{
		
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
	
	public function tplObjectInit( TemplateObject $tpl ):void{
		
		
		
	}
	
	
	
}