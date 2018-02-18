<?php
namespace Sepbin\System\Frame;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Core\IRouteEnable;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Frame\Action;

class FrameControl extends Base implements IFactoryEnable, IRouteEnable
{
	
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
	static public $render = array();
	
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):FrameControl{
		
		if($config_namespace == null) $config_namespace = 'mvc';
		
		return Factory::get(FrameControl::class, $config_namespace, $config_file);
		
	}
	
	
	
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		
		if( $config->check('default_action') ){
			$this->dispatchAction = $config->getStr('default_action');
		}
		
		if( $config->check('render') ){
			
			foreach ( $config->getArrStr('render') as $key => $item ){
				self::addRender($key, $item);
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
	static public function addRender( string $class_type, string $render ) : void{
		
		self::$render[ $class_type ] = $render;
		
	}
	
	/**
	 * 分派并执行渲染界面
	 */
	public function dispatch(){
		
		$action = Action::get($this->module, $this->controller);
		$actionName = $this->dispatchAction;
		
		//反射填入参数并执行方法
		$r = new \ReflectionMethod($action->controller,$actionName.'Action');
		$paramNames = $r->getParameters();
		$requestParams = array();
		
		if(!empty($paramNames)){
			foreach ($paramNames as $item){
				$name = $item->name;
				if( $item->isDefaultValueAvailable() ){
					$def = $item->getDefaultValue();
				}
				$requestParams[] = request()->get($name,$def);
			}
		}
		
		$result = $action->$actionName(...$requestParams);
		getApp()->getResponse()->setContentType( $action->getLastRender()->responseFormat );
		
		return $result;
	}
	
	
	
	
}