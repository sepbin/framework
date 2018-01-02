<?php
namespace Sepbin\System\Mvc;

use Sepbin\System\Core\Base;
use Sepbin\System\Core\Application;
use Sepbin\System\Mvc\Exception\NotFoundException;
use Sepbin\System\Util\InstanceSet;
use Sepbin\System\Mvc\Exception\ActionResultErrorException;
use Sepbin\System\Mvc\Exception\RenderErrorException;
use Sepbin\System\Mvc\Restful\RestfulModel;
use Sepbin\System\Mvc\Restful\RestfulViewRender;
use Sepbin\System\Util\HookRun;
use Sepbin\System\Mvc\Auto\AbsAutoController;
use Sepbin\System\Mvc\Auto\AutoViewRender;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Mvc\Hook\IMvcModelHook;
use Sepbin\System\Mvc\Hook\IMvcRenderHook;

class MvcControl extends Base implements IFactoryEnable
{
	
	
	/**
	 * 要分派的类型名称
	 * 一般不要设置，将由route来指定
	 * @var string
	 */
	public $dispatchClass = 'SepApp\Application\Index\IndexController';
	
	/**
	 * 要分派的方法名称
	 * 一般不要设置，将由route来指定
	 * @var string
	 */
	public $dispatchAction = 'index';
	
	
	/**
	 * 使用的路由
	 * @var AbsRoute[]
	 */
	private $route = array();
	
	
	/**
	 * 渲染器
	 * @var array
	 */
	private $render = array();
	
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ){
		
		return Factory::get(MvcControl::class, $config_namespace, $config_file);
		
	}
	
	static public function _factory( \Sepbin\System\Util\FactoryConfig $config ) : IFactoryEnable{
		
		$instance = new MvcControl();
		
		if( $config->check('default_controller') ){
			$instance->dispatchClass = $config->getStr('default_controller');
		}
		
		if( $config->check('default_action') ){
			$instance->dispatchAction = $config->getStr('default_action');
		}
		
		
		if( $config->check('route') ){
			foreach ($config->getArrStr('route') as $item){
				$instance->addRoute( new $item );
			}
		}
		
		if( $config->check('render') ){
			
			foreach ( $config->getArrStr('render') as $key => $item ){
				$instance->addRender($key, $item);
			}
			
		}
		
		return $instance;
		
	}
	
	
	
	public function _init(){
		
		$this->addRender(RestfulModel::class, RestfulViewRender::class );
		
	}
	
	/**
	 * 加入路由实例
	 * @param AbsRoute $route
	 */
	public function addRoute( AbsRoute $route ){
		
		$this->route[] = $route;
		
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
		
		
		//把路由加入到一个集合，设置集合为一个隧道模式
		$set = new InstanceSet(AbsRoute::class, InstanceSet::CALL_TUNNEL);
		foreach ($this->route as $item){
			$set->add( $item );
		}
		
		//设置当前的路由
		$this->dispatchClass = $set->_findController( $this->dispatchClass );
		
		try {
			$instance = new $this->dispatchClass;
		}catch (\Error $e){
			throw (new NotFoundException())->appendMsg( 'class : '. $this->dispatchClass );
		}
		
		//设置当前的执行方法
		$this->dispatchAction = $set->_findAction( $this->dispatchAction, $instance );
		
		$methodName = $this->getActionName();
		
		if( !method_exists($instance, $methodName) ){
			throw (new NotFoundException())->appendMsg( 'action : ' . $this->dispatchAction );
		}
		
		
		$result = $instance->$methodName();
		
		if( !$result instanceof Model ){
			throw (new ActionResultErrorException())->appendMsg( $this->dispatchClass .' : '. $this->dispatchAction );
		}
		
		
		if( isset($this->render[ get_class($result) ]) ){
			
			$render = new $this->render[ get_class($result) ];
			
			if( !$render instanceof ViewRender ){
				throw (new RenderErrorException())->appendMsg( $this->render[ get_class($result) ] );
			}
			
		}else{
			
			$renderName = HookRun::tunnel(IMvcRenderHook::class, 'renderCreateBefore', ViewRender::class,$instance,$this->dispatchAction);
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