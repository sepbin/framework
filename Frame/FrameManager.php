<?php
namespace Sepbin\System\Frame;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Core\IRouteEnable;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Frame\Action;
use Sepbin\System\Util\HookRun;
use Sepbin\System\Frame\Hook\IMvcRouteHook;

class FrameManager extends Base implements IFactoryEnable, IRouteEnable
{
	
	/**
	 * 要分派的方法名称
	 * 一般不要设置，将由route来指定
	 * @var string
	 */
	static public $action = 'index';
	
	static public $module = 'Index' ;
	
	static public $controller = 'Index';
	
	
	/**
	 * 渲染器
	 * @var array
	 */
	static public $render = array();
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):FrameManager{
		
		if($config_namespace == null) $config_namespace = 'mvc';
		
		return Factory::get(FrameManager::class, $config_namespace, $config_file);
		
	}
	
	
	
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		if( $config->check('default_action') ){
		    
			self::$action = $config->getStr('default_action');
			
		}
		
		if( $config->check('render') ){
			
			foreach ( $config->getArrStr('render') as $key => $item ){
			    
				self::addRender($key, $item);
				
			}
			
		}
		
		FrameManager::addRender(\Sepbin\System\Frame\Redirect\RedirectModel::class, 
		    \Sepbin\System\Frame\Redirect\RedirectRender::class);
		
		FrameManager::addRender(\Sepbin\System\Frame\File\FileModel::class, 
		    \Sepbin\System\Frame\File\FileRender::class);
		
	}
	
	
	/**
	 * 实现路由回调
	 * {@inheritDoc}
	 * @see \Sepbin\System\Core\IRouteEnable::RouteMapper()
	 */
	public function RouteMapper( array $params ){
		
	    
		if( !empty($params['module']) ){
			self::$module = ucfirst( ClassName::underlineToCamel( $params['module'] ) );
		}
		
		if( !empty($params['controller']) ){
			self::$controller = ucfirst( ClassName::underlineToCamel($params['controller']) );
		}
		
		if( !empty($params['action']) ){
			self::$action = $params['action'];
		}
		
		dump( $this->dispatch() );
		
	}
	
	/**
	 * 增加一个渲染器
	 * @param string $class_type 指定的渲染模型类型
	 * @param string $render	渲染器的类名称，必须继承至 Sepbin\System\Mvc\Render，否则在渲染时会抛出异常
	 */
	static public function addRender( string $class_type, string $render ) {
		
		self::$render[ $class_type ] = $render;
		
	}
	
	/**
	 * 分派并执行渲染界面
	 */
	public function dispatch(){
		
		$action = Action::get(self::$module, self::$controller)->setRequestType( getApp()->request->getRequestType() );
		
		self::$action = HookRun::tunnel(IMvcRouteHook::class, 'ActionBefore', self::$action);
		
		$actionName = self::$action;
		
		$requestParams = array();
		
		//反射填入参数并执行方法
		if( method_exists($action->controller, $actionName.'Action') ){
    		$r = new \ReflectionMethod($action->controller,$actionName.'Action');
    		$paramNames = $r->getParameters();
    		if(!empty($paramNames)){
    			foreach ($paramNames as $item){
    				$name = $item->name;
    				$def = null;
    				$type = null;
    				if( $item->hasType() ){
    				    $type = $item->getType().'';
    				}
    				
    				if( $item->isDefaultValueAvailable() ){
    					$def = $item->getDefaultValue();
    				}
    				
    				$val = request()->get($name);
    				
    				if( $def != null && empty($val) ) $val = $def;
    				
    				if( $type == 'int' ) $val = intval($val);
    				if( $type == 'string' ) $val = $val.'';
    				if( $type == 'array' ){
    				    if( !is_array($val) ) $val = [$val];
    				}
    				if( $type == 'float' ) $val = floatval($val);
    				
    				$requestParams[] = $val;
    			}
    		}
		}
		
		$result = $action->$actionName(...$requestParams);
		
		return $result;
	}
	
	
	
	
}