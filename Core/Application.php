<?php

namespace Sepbin\System\Core;

use Sepbin\System\Core\Exception\RepeatHookException;
use Sepbin\System\Util\InstanceSet;
use Sepbin\System\Util\InstanceManager;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\FactoryConfig;
use Sepbin\System\Util\Factory;
use Sepbin\System\Core\Hook\IApplicationHook;
use Sepbin\System\Route\BasicRoute;
use Sepbin\System\Route\IRoute;
use Sepbin\System\Core\Exception\RepeatApplicationException;

/**
 * 应用入口
 * 
 * @author joson
 *        
 */
class Application extends Base implements IFactoryEnable {
	
	
	private $debug = true;
	
	private $debugInfo = true;
	
	public $version = '3.0.1-alpha';
	
	/**
	 * 时区
	 * 
	 * @var string
	 */
	public $dateTimezone;
	
	/**
	 * 默认语言
	 * 
	 * @var string
	 */
	public $defaultLang;
	
	
	/**
	 * 字符编码
	 * 
	 * @var string
	 */
	public $charset;
	
	/**
	 * 默认路径
	 * @var string
	 */
	public $defaultPath = '';
	
	/**
	 * 是否开启http重写
	 * @var string
	 */
	public $httpRewrite = false;
	
	
	/**
	 * 服务器是否开启x-sendfile
	 * 此服务用于发送文件，请参考手册`文件上传/下载`一节
	 * @var string
	 */
	public $xSendfile = false;
	
	
	/**
	 * 默认使用的协议
	 * @var string
	 */
	public $defaultScheme = 'http';
	
	/**
	 * 自动转换模型时的默认格式
	 * @var string
	 */
	public $defaultDataFormat = 'json';
	
	/**
	 * 
	 * @var IRoute
	 */
	private $route;
	
	private $process = array ();
	
	private $errHandler = array ();
	
	private $hook = array ();
	
	
	/**
	 * 应用的请求对象
	 * 
	 * @var \Sepbin\System\Core\Request
	 */
	public $request;
	
	/**
	 * 应用的响应对象
	 * 
	 * @var \Sepbin\System\Http\Response
	 */
	public $response;
	
	
	
	private $starttime;
	private $startmemory;
	
	
	function __construct(){
	    static $instance;
	    if( $instance != null ){
	        throw new RepeatApplicationException();
	    } 
	    $instance = 1;
	}
	
	static public function getInstance(string $config_namespace = null, string $config_file = null, string $config_path = CONFIG_DIR): Application {
		
	    return Factory::get ( Application::class, $config_namespace, $config_file, $config_path );
	    
	}
	
	public function _init(FactoryConfig $config) {
	    
	    set_error_handler ( array ( $this, 'error' ) );
	    set_exception_handler ( array ( $this, 'exception' ) );
	    
		$this->debug = $config->getBool ( 'debug', true );
		$this->debugInfo = $config->getBool('debug_info', false);
		$this->charset = $config->getStr ( 'charset', 'utf8' );
		
		if ($config->check ( 'timezone' )) {
			$this->dateTimezone = $config->getStr ( 'timezone' );
		}
		
		if ($config->check ( 'language' )) {
			$this->defaultLang = $config->getStr ( 'language', 'zh-CN' );
		}
		
		if( $config->check('default_data_format') ){
			$this->defaultDataFormat = $config->getStr('default_data_format');
		}
		
		$this->xSendfile = $config->getBool('x_sendfile',false);
		
		
		$this->httpRewrite = $config->getBool ( 'rewrite', false );
		$this->defaultPath = $config->getStr('default_path','');
		$this->defaultScheme = $config->getStr('default_scheme', 'http');
		
		$route = $config->getStr('route',BasicRoute::class);
		$this->route = new $route;
		
		$routes = $config->getArr('routes');
		
		foreach ($routes as $item){
		    if( is_array($item) ){
		        
		        $this->route->addRoute($item['rule'], $item['delegate'], isset($item['params'])?$item['params']:[] );
		        
		    }
		}
		
		
		$hooks = $config->getArr('hooks');
		foreach ($hooks as $interface_name => $instance){
		    $this->registerHook($interface_name, $instance);
		}
		
		$configs = $config->getArrStr('load_conf');
		foreach ($configs as $item){
		    config()->addFile($item);
		}
	    
		if ($this->debug) {
			$this->starttime = explode ( ' ', microtime () );
			$this->startmemory = memory_get_usage ();
		}
		
		
		$this->request = new Request();
		$this->response = new Response();
		
	}
	
	/**
	 * 注册钩子
	 * 
	 * @param string $interface_name 接口名称
	 * @param string|object $hook_name_or_instance 注册的类名或实例
	 */
	final public function registerHook(string $interface_name, $hook_name_or_instance) {
	    
		if (isset ( $this->hook [$interface_name] ) 
		    && is_string ( $hook_name_or_instance ) 
		    && in_array ( $hook_name_or_instance, $this->hook [$interface_name] )) {
			
			throw (new RepeatHookException ())->appendMsg ( $interface_name . ' -> ' . $hook_name_or_instance );
			
		}
		
		$this->hook [$interface_name] [] = $hook_name_or_instance;
		
	}
	
	/**
	 * 注册错误钩子
	 * 
	 * @param int $errorCode        	
	 * @param \Closure $func        	
	 * @return void
	 */
	final public function registerErrorHandler(int $error_code, \Closure $func) {
	    
		$this->errHandler [$error_code] = $func;
		
	}
	
	/**
	 * 执行HOOK
	 * 
	 * @param string $name 接口名
	 * @param string $method_name 方法名
	 * @param int $call_type 执行方式 InstanceSet::CALL_XX
	 * @param unknown ...$params        	
	 * @return void|array
	 */
	final public function hook(string $name, string $method_name, int $call_type, ...$params) {
		
		$set = new InstanceSet ( $name, $call_type );
		$instanceManager = InstanceManager::getInstance ();
		
		if (! empty ( $this->hook [$name] )) {
			foreach ( $this->hook [$name] as $item ) {
				
				if (is_string ( $item )) {
					$set->add ( $instanceManager->get ( $item ) );
				} else {
					$set->add ( $item );
				}
			}
		}
		
		$method_name = '_' . $method_name;
		
		return $set->$method_name ( ...$params );
	}
	
	
	/**
	 * 注册类库
	 * @param string $namespace_pre    命名空间前缀
	 * @param string $dir              物理路径
	 */
	final public function registerLib($namespace_pre, $dir) {
		_registerLib ( $namespace_pre, $dir );
	}
	
	
	/**
	 * 增加路由
	 * @param string $rule                 规则
	 * @param string|\Closure $delegate    响应方法
	 * @param array $params                参数
	 * @param array $restrict              限定
	 */
	final public function addRoute($rule, $delegate, $params = array(), $restrict = array()) {
	    
	    $this->route->addRoute($rule, $delegate, $params, $restrict);
	    
	}
	
	
	private function setLang() {
		
		date_default_timezone_set ( $this->dateTimezone );
		setlocale ( LC_ALL, $this->request->requestLang . '.' . $this->charset );
		putenv ( "LANGUAGE=" . $this->request->requestLang . '.' . $this->charset );
		
		/**
		 * 绑定公共语言库
		 */
		bindtextdomain ( 'Application', APP_DIR . '/Locale' );
		bind_textdomain_codeset ( 'Application', getApp ()->charset );
		
	}
	
	
	
	/**
	 * 开始运行
	 * 
	 * @return null
	 */
	final public function run() {
		
		$this->hook ( IApplicationHook::class, 'applicationStart', InstanceSet::CALL_VOID, $this );
		
		$this->setLang ();
		
		if (getApp()->request->getRequestType () != Request::REQUEST_TYPE_CONSOLE) {
			
			if ($_SERVER ['PHP_SELF']) {
				$path = $_SERVER ['PHP_SELF'];
			} else {
				$path = $_SERVER ['REQUEST_URI'];
				if (strpos ( $path, '?' ) !== false) {
					$path = substr ( $path, 0, strpos ( $path, '?' ) );
				}
			}
			
			$path = substr ( $path, strlen ( HTTP_ROOT ) );
			
			$path = str_replace ( '/index.php', '', $path );
			$path = ltrim ( $path, '/' );
			
			if($path == ''){
				$path = getApp()->defaultPath;
			}
			
			$host = $_SERVER['HTTP_HOST'];
			
		} else {
			
			if ( isset($_SERVER['argv'][1]) &&  substr ( $_SERVER ['argv'] [1], 0, 1 ) == '-' ) {
				$path = '';
			} else {
				$path = isset ( $_SERVER ['argv'] [1] ) ? $_SERVER ['argv'] [1] : '';
			}
			
			$simulation = request()->get('simulation',false);
			
			if( !$simulation && !is_string($simulation) ){
				$host = 'cli';
			}else{
				$host = $simulation;
			}
			
			
		}
		
		
		$this->runRoute($host,$path);
		
		$this->hook ( IApplicationHook::class, 'applicationEnd', InstanceSet::CALL_VOID, $this );
		
		if ($this->debug) {
			
			$this->outAssist();
			
		}
		
		$this->response->flush ();
	}
	
	final public function runRoute( $host ,$path ){
		
		$this->route->route( $host, $path );
		
	}
	
	
	/**
	 * 获取应用请求对象
	 * 
	 * @return \Sepbin\Http\Request
	 */
	final public function getRequest(): Request {
		return $this->request;
	}
	
	
	
	/**
	 * 获取应用响应对象
	 * 
	 * @return \Sepbin\Http\Response
	 */
	final public function getResponse(): Response {
		return $this->response;
	}
	
	
	
	/**
	 * 是否处于调试模式
	 * @return bool
	 */
	final public function isDebug(): bool {
		return $this->debug;
	}
	
	
	
	
	
	/**
	 * 脚本错误
	 */
	final public function error($errno, $errstr, $errfile, $errline) {
		
		switch ($errno) {
			
			case E_USER_ERROR :
				
				AppInfo::Record ( $errno, '错误', $errstr . " [$errfile( line $errline )]" );
				break;
			
			case E_USER_WARNING :
				AppInfo::Record ( $errno, '警告', $errstr . " [$errfile( line $errline )]" );
				break;
			
			case E_USER_NOTICE :
				AppInfo::Record ( $errno, '通知', $errstr . " [$errfile( line $errline )]" );
				break;
			
			case E_WARNING :
				AppInfo::Record ( $errno, '系统警告', $errstr . " [$errfile( line $errline )]" );
				break;
			
			case E_NOTICE :
				AppInfo::Record ( $errno, '系统通知', $errstr . " [$errfile( line $errline )]" );
				break;
		}
		
		if (isset ( $this->errHandler [$errno] )) {
			$this->errHandler [$errno] ( $errno, $errstr, $errfile, $errline );
		}
		
		$this->hook ( IApplicationHook::class, 'applicationWarning', InstanceSet::CALL_VOID, $errno, $errstr, $errfile, $errline );
	
	}
	
	
	/**
	 * 输出debug模式下的辅助信息
	 */
	protected function outAssist(){
		
		if( !$this->debugInfo ) return ;
		
		$this->response->bufferOut ( function () {
		    
			$endtime = explode ( ' ', microtime () );
			$runtime = $endtime [0] + $endtime [1] - ($this->starttime [0] + $this->starttime [1]);
			$runtime = round ( $runtime, 5 );
			
			AppInfoView::$app = $this;
			AppInfoView::$runtime = $runtime;
			AppInfoView::$runmemory = round ( (memory_get_usage () - $this->startmemory) / 1024 / 1024, 3 );
			
			if ($this->request->getRequestType () == Request::REQUEST_TYPE_CONSOLE) {
				
				AppInfoView::string();
				
			}
			
			if ($this->request->getRequestType () == Request::REQUEST_TYPE_BROSWER) {
				
				AppInfoView::html ();
				
			}
			
			if ($this->request->getRequestType () == Request::REQUEST_TYPE_POST) {
				
				AppInfoView::data ();
				
			}
			
			
		} );
			
	}
	
	/**
	 * 应用异常
	 */
	final public function exception($e) {
		
		ob_end_clean();
		ob_clean();
		
		if (isset ( $this->errHandler [$e->getCode ()] )) {
			
			$this->errHandler[$e->getCode()]( $e->getCode (), $e->getMessage (), $e->getFile (), $e->getLine () );
			
		}else{
		    
			AppExceptionView::$app = $this;
			AppExceptionView::$err = $e;
			
			if ($this->request->getRequestType () == Request::REQUEST_TYPE_CONSOLE) {
				AppExceptionView::string ();
			}
			
			if ($this->request->getRequestType () == Request::REQUEST_TYPE_BROSWER) {
				
				AppExceptionView::html ();
				
			}
			
			if ($this->request->getRequestType () == Request::REQUEST_TYPE_POST) {
				
				AppExceptionView::data ();
				
			}
			
		    
		}
		
		try {
			$this->hook ( IApplicationHook::class, 'applicationException', InstanceSet::CALL_VOID, $e->getCode (), $e->getMessage (), $e->getFile (), $e->getLine () );
		} catch ( \Exception $e ) {
		}
		
		$this->response->flush ();
		
	}
}