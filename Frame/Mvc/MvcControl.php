<?php
namespace Sepbin\System\Frame\Mvc;

use Sepbin\System\Core\Base;
use Sepbin\System\Core\Application;
use Sepbin\System\Frame\Mvc\Exception\NotFoundException;
use Sepbin\System\Frame\Mvc\Exception\ActionResultErrorException;
use Sepbin\System\Frame\Mvc\Exception\RenderErrorException;
use Sepbin\System\Util\HookRun;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Frame\Hook\IMvcModelHook;
use Sepbin\System\Frame\Hook\IMvcRenderHook;
use Sepbin\System\Core\IRouteEnable;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Frame\Model;
use Sepbin\System\Frame\Mvc\ViewRender;
use Sepbin\System\Frame\AbsRender;
use Sepbin\System\Frame\AbsController;

class MvcControl extends Base implements IFactoryEnable, IRouteEnable
{
	
	/**
	 * 要分派的类型名称
	 * 一般不要设置，交由route来指定
	 * @var string
	 */
	public $dispatchClass = 'SepApp\Application\Index\IndexController';
	
	/**
	 * 要分派的方法名称
	 * 一般不要设置，将由route来指定
	 * @var string
	 */
	public $dispatchAction = 'index';
	
	
	
	public $module = 'Index' ;
	
	
	public $controller = 'Index';
	
	
	/**
	 * 渲染器
	 * @var array
	 */
	private $render = array();
	
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ){
		
		if($config_namespace == null) $config_namespace = 'mvc';
		
		return Factory::get(MvcControl::class, $config_namespace, $config_file);
		
	}
	
	
	
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		
		if( $config->check('default_controller') ){
			$this->dispatchClass = $config->getStr('default_controller');
		}
		
		if( $config->check('default_action') ){
			$this->dispatchAction = $config->getStr('default_action');
		}
		
		if( $config->check('render') ){
			
			foreach ( $config->getArrStr('render') as $key => $item ){
				$this->addRender($key, $item);
			}
			
		}
		
		//$this->addRender(RestfulModel::class, RestfulViewRender::class );
// 		$this->addRender(ResultModel::class, ResultViewRender::class);
		
	}
	
	
	/**
	 * 实现路由回调
	 * {@inheritDoc}
	 * @see \Sepbin\System\Core\IRouteEnable::RouteMapper()
	 */
	public function RouteMapper( array $params ){
		
		
		if( !empty($params['module']) ){
			$this->module = ucfirst( ClassName::underlineToCamel( $params['module'] ) );
		}
		
		if( !empty($params['controller']) ){
			$this->controller = ucfirst( ClassName::underlineToCamel($params['controller']) );
		}
		
		$this->dispatchClass = 'SepApp\Application\\'.$this->module .'\\'.$this->controller.'Controller' ;
		
		if(!empty($params['action'])){
			$this->dispatchAction = $params['action'];
		}
		
		dump( $this->dispatch() );
		
	}
	
	
	
	/**
	 * 增加一个渲染器
	 * @param string $class_type 指定的渲染模型类型
	 * @param string $render	渲染器的类名称，必须继承至 Sepbin\System\Mvc\Render，否则在渲染时会抛出异常
	 */
	public function addRender( string $class_type, string $render ) : void{
		
		$this->render[ $class_type ] = $render;
		
	}
	
	/**
	 * 分派
	 */
	public function dispatch(){
	
		if( !class_exists( $this->dispatchClass ) ){
			throw (new NotFoundException())->appendMsg( 'class : '. $this->dispatchClass );
		}
		
		/**
		 * 
		 * @var AbsController $instance
		 */
		$instance = Factory::get($this->dispatchClass);
		$instance->moduleName = $this->module;
		$instance->controllerName = $this->controller;
		$instance->actionName = $this->dispatchAction;
		$instance->_start();
		
		$methodName = $this->getActionName();
		
		if( !method_exists($instance, $methodName) ){
			throw (new NotFoundException())->appendMsg( 'action : ' . $this->dispatchAction );
		}
		
		
		$result = $instance->$methodName();
		
		if( !$result instanceof Model ){
			throw (new ActionResultErrorException())->appendMsg( $this->dispatchClass .' : '. $this->dispatchAction );
		}
		
		$instance->_end();
		
		
		if( isset($this->render[ get_class($result) ]) ){
			
			$render = new $this->render[ get_class($result) ];
			
			if( !$render instanceof AbsRender ){
				throw (new RenderErrorException())->appendMsg( $this->render[ get_class($result) ] );
			}
			
		}else{
			
			$renderName = HookRun::tunnel(IMvcRenderHook::class, 'renderCreateBefore', ViewRender::class, $instance, $this->dispatchAction);
			$render = new $renderName();
			
		}
		
		HookRun::void(IMvcModelHook::class, 'modelRenderBefore', $result);
		
		$render->setRouteInfo($instance, $this->dispatchAction);
		
		return $render->get($result);
		
	}
	
	
	private function getActionName():string{
		
		return $this->dispatchAction.'Action';
		
	}
	
	
}