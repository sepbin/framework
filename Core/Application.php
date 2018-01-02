<?php
namespace Sepbin\System\Core;

use Sepbin\System\Http\Request;
use Sepbin\System\Http\Response;
use Sepbin\System\Core\Exception\RepeatHookException;
use Sepbin\System\Util\InstanceSet;
use Sepbin\System\Util\InstanceManager;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\FactoryConfig;
use Sepbin\System\Util\Factory;

/**
 * 应用入口
 * @author joson
 *
 */
class Application extends Base implements IFactoryEnable
{
    
	
	
	private $debug = true;
	
	
	/**
	 * 时区
	 * @var string
	 */
	public $dateTimezone;
	
	
	
	/**
	 * 默认语言
	 * @var string
	 */
	public $defaultLang;
	
	
	/**
	 * 当前语言
	 * @var string
	 */
	public $language;
	
	
	public $charset;
	
	
	
	
    private $process = array();
    
    
    private $errHandler = array();
    
    
    private $hook = array();
    
    
    /**
     * 应用的请求对象
     * @var \Sepbin\System\Http\Request
     */
    private $request;
    
    
    /**
     * 应用的响应对象
     * @var \Sepbin\System\Http\Response
     */
    private $response;
    
    private $starttime;
    
    private $startmemory;
    
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ) : Application{
        
        return Factory::get( Application::class, $config_namespace, $config_file, $config_path );
        
    }
    
    static public function _factory( FactoryConfig $config ) : IFactoryEnable{
    	
    	$app = new Application();
    	$app->debug = $config->getBool('debug',true);
    	$app->charset = $config->getStr('charset','utf8');
    	
    	if( $config->check('timezone') ){
    		$app->dateTimezone = $config->getStr('timezone');
    	}
    	
    	if( $config->check('language') ){
    		$app->defaultLang = $config->getStr('language','zh-CN');
    		$app->language = $app->defaultLang;
    	}
    	
    	return $app;
    	
    }
    
    
    public function _init(){
    	
    	if( $this->debug ){
    		$this->starttime = explode(' ',microtime());
    		$this->startmemory = memory_get_usage();
    	}
    	
    	$this->request = new Request();
    	$this->response = Response::getInstance('application.response');
    	
    	set_error_handler(array($this,'error'));
    	set_exception_handler(array($this,'exception'));
    	
    	
    }
    
    
    
    /**
     * 注册钩子
     * @param string $interface_name  接口名称
     * @param string|object $hook_name_or_instance	注册的类名或实例
     */
    public function registerHook( string $interface_name, $hook_name_or_instance ): void {
        
    	if( isset($this->hook[$interface_name]) 
    			&& is_string($hook_name_or_instance) 
    			&& in_array($hook_name_or_instance, $this->hook[$interface_name]) ){
    		
    		throw (new RepeatHookException() )->appendMsg( $interface_name.' -> '.$hook_name_or_instance );
    		
    	}
    	
    	$this->hook[ $interface_name ][] = $hook_name_or_instance;
    	
    }
    
    
    /**
     * 注册服务
     */
    public function registerService(){
         
    }
    
    
    /**
     * 注册错误钩子
     * @param int $errorCode
     * @param \Closure $func
     * @return void
     */
    public function registerErrorHandler( int $error_code, \Closure $func ) {
        
        $this->errHandler[ $error_code ] = $func;
        
    }
    
    
    
    /**
     * 执行HOOK
     * @param string $name 接口名
     * @param string $method_name  方法名
     * @param int $call_type 执行方式 InstanceSet::CALL_XX
     * @param unknown ...$params
     * @return void|array
     */
    public function hook( string $name, string $method_name, int $call_type, ...$params ){
    	
    	if( empty($this->hook[$name]) ){
    		return ;
    	}
    	
    	$set = new InstanceSet($name, $call_type);
    	$instanceManager = InstanceManager::getInstance();
    	
    	foreach ( $this->hook[$name] as $item ){
    		
    		if( is_string($item) ){
    			$set->add( $instanceManager->get($item) );
    		}else{
    			$set->add( $item );	
    		}
    		
    	}
    	
    	$method_name = '_'.$method_name;
    	
    	return $set->$method_name( ...$params );
    	
    }
    
    /**
     * 运行过程
     * @param \Closure $func  运行的匿名函数
     * @return void
     */
    public function process( \Closure $func ){
        
        $this->process[] = $func;
        
    }

    
    private function setLang(){
    	
    	date_default_timezone_set( $this->dateTimezone );
    	setlocale(LC_ALL, $this->language.'.'.$this->charset);
    	putenv("LANGUAGE=".$this->language.'.'.$this->charset);
    	
    	/**
    	 * 绑定公共语言库
    	 */
    	bindtextdomain('Application', APP_DIR.'/Locale');
    	bind_textdomain_codeset('Application', getApp()->charset);
    	
    }
    
    
    /**
     * 开始运行
     * @return null
     */
    public function run(){
    	
    	$this->hook(IApplicationHook::class, 'applicationStart', InstanceSet::CALL_VOID, $this );
        
    	$this->setLang();  
    	
        if( !empty($this->process) ){
            foreach ($this->process as $item){
                $item();
            }
        }
        
        $this->hook(IApplicationHook::class, 'applicationEnd', InstanceSet::CALL_VOID, $this );
        
        if( $this->debug ){
            
            $this->response->bufferOut(function(){
            	$endtime = explode(' ',microtime());
            	$runtime = $endtime[0]+$endtime[1]-($this->starttime[0]+$this->starttime[1]);
            	$runtime = round($runtime,5);
            	AppInfoView::$app = $this;
            	AppInfoView::$runtime = $runtime;
            	AppInfoView::$runmemory = round( (memory_get_usage() - $this->startmemory)/1024/1024, 3 );
            	
            	if( $this->request->getRequestType() == Request::REQUEST_TYPE_CONSOLE ){
            		AppInfoView::string();
            	}elseif ( $this->request->getRequestType() == Request::REQUEST_TYPE_POST ){
            		
            	}else{
            		AppInfoView::html();
            	}
            });
            
            
            
        }
        
        $this->response->flush();
        
    }
    
    
    /**
     * 获取应用请求对象
     * @return \Sepbin\Http\Request
     */
    public function getRequest():Request{
    	return $this->request;
    }
    
    
    /**
     * 获取应用响应对象
     * @return \Sepbin\Http\Response
     */
    public function getResponse():Response{
    	return $this->response;
    }
    
    
    public function isDebug():bool{
    	return $this->debug;
    }
    
    
    /**
     * 脚本错误
     */
    public function error( $errno, $errstr, $errfile, $errline ){
        
        switch ( $errno ){
            
            case E_USER_ERROR:
                
                AppInfo::Record($errno, '错误', $errstr." [$errfile( line $errline )]");
                break;
                
            case E_USER_WARNING:
                AppInfo::Record($errno, '警告', $errstr." [$errfile( line $errline )]");
                break;
                
            case E_USER_NOTICE:
                AppInfo::Record($errno, '通知', $errstr." [$errfile( line $errline )]");
                break;
                
            case E_WARNING:
                AppInfo::Record($errno, '系统警告', $errstr." [$errfile( line $errline )]");
                break;
                
            case E_NOTICE:
                AppInfo::Record($errno, '系统通知', $errstr." [$errfile( line $errline )]");
                break;
            
        }
        
        if( isset($this->errHandler[ $errno ]) ){
            $this->errHandler[ $errno ]( $errno, $errstr, $errfile, $errline );
        }
        
        $this->hook(IApplicationHook::class, 'applicationWarning', InstanceSet::CALL_VOID, $errno, $errstr, $errfile, $errline );
        
    }
    
    /**
     * 应用异常
     */
    public function exception( $e ){
    	
    	//ob_clean();
    	
	    $this->response->bufferOut(function() use ($e){
		    AppExceptionView::$app = $this;
		    AppExceptionView::$err = $e;
		    if( $this->request->getRequestType() == Request::REQUEST_TYPE_CONSOLE ){
		    	AppExceptionView::string();
		    }elseif ( $this->request->getRequestType() == Request::REQUEST_TYPE_POST ){
		    	AppExceptionView::json();
		    }else{
		    	AppExceptionView::html();
		    }
	    });
	    	
    	
    	
        if( isset($this->errHandler[ $e->getCode() ]) ){
            $this->errHandler[ $e->getCode ]( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
        }
        
        try {
        	$this->hook(IApplicationHook::class, 'applicationException', InstanceSet::CALL_VOID, $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
        }catch ( \Exception $e){
        	
        }
        
        $this->response->flush();
        
    }
    
    
}