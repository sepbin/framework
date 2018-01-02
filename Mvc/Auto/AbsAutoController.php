<?php
namespace Sepbin\System\Mvc\Auto;

use Sepbin\System\Mvc\AbsController;
use Sepbin\System\Http\Request;
use Sepbin\System\Http\Response;
use Sepbin\System\Mvc\Hook\IMvcRenderHook;

/**
 * 需要自动渲染的控制器
 * 继承此控制器的对象，返回的基础Model将通过AutoViewRender渲染，而不是ViewRender
 * @author joson
 *
 */
abstract class AbsAutoController extends AbsController implements IMvcRenderHook
{
	
	protected $autoDataType = Response::DATA_TYPE_JSON;
	
	function __construct(){
		
		getApp()->registerHook(IMvcRenderHook::class, $this);
		
		if( getApp()->getRequest()->getRequestType() == Request::REQUEST_TYPE_BROSWER ){
			
			getApp()->getResponse()->setContentType( Response::DATA_TYPE_HTML );
			
		}else{
			
			getApp()->getResponse()->setContentType( $this->autoDataType );
			
		}
		
		parent::__construct();
	}
	
	public function renderCreateBefore( string $render_name, \Sepbin\System\Mvc\AbsController $controller, string $action ) : string{
		
		if( $controller instanceof AbsAutoController ){
			return AutoViewRender::class;
		}
		
		return $render_name;
		
	}
	
}