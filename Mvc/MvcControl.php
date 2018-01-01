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

class MvcControl extends Base
{
	
	/**
	 * 
	 * @var Application
	 */
	private $app;
	
	/**
	 * 要分派的类型名称
	 * 一般不要设置，将由route来指定
	 * @var string
	 */
	public $dispatchClass;
	
	
	/**
	 * 要分派的方法名称
	 * 一般不要设置，将由route来指定
	 * @var string
	 */
	public $dispatchAction;
	
	
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
	
	
	function __construct( Application $app ){
		
		$this->app = $app;
		
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
		
		$defaultController = 'SepApp\Application\Index\IndexController';
		$defaultAction = 'index';
		
		
		//把路由加入到一个集合，设置集合为一个隧道模式
		$set = new InstanceSet(AbsRoute::class, InstanceSet::CALL_TUNNEL);
		foreach ($this->route as $item){
			$set->add( $item );
		}
		
		//设置当前的路由
		$this->dispatchClass = $set->_findController( $defaultController );
		
		try {
			$instance = new $this->dispatchClass;
		}catch (\Error $e){
			throw (new NotFoundException())->appendMsg( 'class : '. $this->dispatchClass );
		}
		
		//设置当前的执行方法
		$this->dispatchAction = $set->_findAction( $defaultAction, $instance );
		
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
			
			$render = new ViewRender();
			
		}
		
		HookRun::void(IMvcHook::class, 'modelRenderBefore', $result);
		
		$render->setRouteInfo($this->dispatchClass, $this->dispatchAction);
		
		return $render->get($result);
		
	}
	
	
	
	
	private function getActionName():string{
		
		return $this->dispatchAction.'Action';
		
	}
	
	
}