<?php
namespace Sepbin\System\Frame;


use Sepbin\System\Util\Factory;
use Sepbin\System\Frame\Exception\NotFoundException;
use Sepbin\System\Frame\Exception\ActionResultErrorException;
use Sepbin\System\Core\Base;
use Sepbin\System\Frame\Exception\RenderErrorException;
use Sepbin\System\Util\HookRun;
use Sepbin\System\Frame\Hook\IMvcRenderHook;
use Sepbin\System\Frame\Mvc\ViewRender;
use Sepbin\System\Frame\Hook\IMvcModelHook;

class Action extends Base
{
	
	
	private $moduleName;
	
	private $controllerName;
	
	
	/**
	 * 
	 * @var AbsRender
	 */
	private $render;
	
	
	/**
	 * 模拟的请求方法，这个方法的值请使用 request类中的常量
	 * 设置这个值决定返回的结果被渲染成何种格式
	 * @var string
	 */
	private $requestType;
	
	
	/**
	 * 控制器实例
	 * @var AbsController
	 */
	public $controller;
	
	
	static public function get( string $module_name, string $controller_name ) : Action{
		
		$instance = new Action();
		$instance->moduleName = $module_name;
		$instance->controllerName = $controller_name;
		
		$dispatchClass = 'SepApp\Application\\'.$instance->moduleName .'\\'.$instance->controllerName.'Controller' ;
		
		if( !class_exists( $dispatchClass ) ){
			throw (new NotFoundException())->appendMsg( 'class : '. $dispatchClass );
		}
		
		$instance->controller = Factory::get($dispatchClass);
		
		if( !$instance->controller instanceof AbsController ){
			throw (new NotFoundException())->appendMsg( 'class : '. $dispatchClass );
		}
		
		$instance->controller->moduleName = $module_name;
		$instance->controller->controllerName = $controller_name;
		
		return $instance;
		
	}
	
	
	/**
	 * 设置模拟的请求方法
	 * @param string $type
	 * @return Action
	 */
	public function setRequestType(string $type) : Action{
		
		$this->requestType = $type;
		return $this;
		
	}
	
	
	/**
	 * 执行action的魔术方法，注意方法名称不要带上Action后缀
	 * @param unknown $name
	 * @param unknown $args
	 * @return unknown
	 */
	public function __call( $name, $args ) {
	    
		$action = $this->getActionMethod($name);
		
		//如果调用的方法在同一个控制器中，则直接调用并返回结果
		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,2);
		array_shift($backtrace);
		
		//否则跨控制器执行方法
		if( !method_exists($this->controller, $action) && !method_exists($this->controller, '_every') ){
		    throw (new NotFoundException())->appendMsg( 'action : ' . $action );
		}
		
		
		if( $backtrace[0]['object'] == $this->controller ){
			
		    if( method_exists($this->controller, $action) ){
		         $result = $this->controller->$action(...$args);
		    }else{
		         $result = $this->controller->_every( $name );
		    }
			
		}else{
			
			$this->controller->actionName = $action;
			if( !$this->controller->_isStart ) $this->controller->_start();
			
			if( method_exists($this->controller, $action) ){
			    $result = $this->controller->$action( ...$args );
			}else{
			    $result = $this->controller->_every( $name );
			}
			
			if( !$this->controller->_isStart ) $this->controller->_end();
			
		}
		
		//判断结果正确性
		if( !$result instanceof Model ){
			throw (new ActionResultErrorException())->appendMsg( $this->moduleName .' : '.$this->controllerName.' -> '. $action );
		}
		
		if( isset(FrameManager::$render[ get_class($result) ]) ){
		    $this->render = new FrameManager::$render[ get_class($result) ];
			if( !$this->render instanceof AbsRender ){
			    throw (new RenderErrorException())->appendMsg( FrameManager::render[ get_class($result) ] );
			}
			
		}else{
			$renderName = HookRun::tunnel(IMvcRenderHook::class, 'renderCreateBefore', ViewRender::class, $this->controller, $name);
			$this->render = new $renderName();
		}
		
		HookRun::void(IMvcModelHook::class, 'modelRenderBefore', $result);
		
		$this->render->setRouteInfo($this->controller, $name);
		
		if($this->requestType != null){
			$this->render->requestType = $this->requestType;
		}
		return $this->render->get($result);
		
	}
	
	public function getLastRender(){
		return $this->render;
	}
	
	private function getActionMethod( $name ){
		
		return $name.'Action';
		
	}
	
}