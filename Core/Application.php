<?php
namespace Sepbin\System\Core;

use Sepbin\System\Http\Request;
use Sepbin\System\Http\Response;
use Sepbin\System\Core\Exception\RepeatHookException;
use Sepbin\System\Util\InstanceSet;
use Sepbin\System\Util\InstanceManager;

/**
 * 应用入口
 * @author joson
 *
 */
class Application extends Base
{
    
    private $process = array();
    
    private $errHandler = array();
    
    private $hook = array();
    
    
    /**
     * 应用的请求对象
     * @var \Sepbin\Http\Request
     */
    private $request;
    
    
    /**
     * 应用的响应对象
     * @var \Sepbin\Http\Response
     */
    private $response;
    
    private $starttime;
    
    private $startmemory;
    
    
    static function getInstance() : Application{
        
        static $instance = null;
        
        if( $instance == null ){
            $instance = new Application();
        }
        
        return $instance;
        
    }
    
    function __construct(){
        
        if( DEBUG ){
            $this->starttime = explode(' ',microtime());
            $this->startmemory = memory_get_usage();
        }
        
        $this->request = new Request();
        $this->response = new Response();
        
        set_error_handler(array($this,'error'));
        
        set_exception_handler(array($this,'exception'));
        
    }
    
    
    /**
     * 注册钩子
     */
    public function registerHook( string $name, string $hook_name ) {
        
    	if( isset($this->hook[$name]) && in_array($hook_name, $this->hook[$name]) ){
    		throw (new RepeatHookException() )->appendMsg( $name.' -> '.$hook_name );
    	}
    	
    	$this->hook[ $name ][] = $hook_name;
    	
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
    		
    		$set->add( $instanceManager->get($item) );
    		
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
    
    /**
     * 开始运行
     * @return null
     */
    public function run(){
    	
    	$this->hook('\Sepbin\System\Core\IApplicationHook', 'applicationStart', InstanceSet::CALL_VOID, $this );
        
        if( !empty($this->process) ){
            foreach ($this->process as $item){
                $item();
            }
        }
        
        $this->hook('\Sepbin\System\Core\IApplicationHook', 'applicationEnd', InstanceSet::CALL_VOID, $this );
        
        if( DEBUG ){
            
            $endtime = explode(' ',microtime());
            $runtime = $endtime[0]+$endtime[1]-($this->starttime[0]+$this->starttime[1]);
            $runtime = round($runtime,5);
            AppInfoView::$runtime = $runtime;
            AppInfoView::$runmemory = round( (memory_get_usage() - $this->startmemory)/1024/1024, 3 );
            AppInfoView::html();
            
        }
        
        
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
    
    
    /**
     * 脚本错误
     */
    public function error( $errno, $errstr, $errfile, $errline ){
        
        switch ( $errno ){
            
            case E_USER_ERROR:
                
                AppInfo::Record($errno, '致命错误', $errstr." [$errfile( line $errline )]");
                break;
                
            case E_USER_WARNING:
                AppInfo::Record($errno, '警告信息', $errstr." [$errfile( line $errline )]");
                break;
                
            case E_USER_NOTICE:
                AppInfo::Record($errno, '通知信息', $errstr." [$errfile( line $errline )]");
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
        
        $this->hook('\Sepbin\System\Core\IApplicationHook', 'applicationWarning', InstanceSet::CALL_VOID, $errno, $errstr, $errfile, $errline );
        
    }
    
    /**
     * 应用异常
     */
    public function exception( $e ){
        
        AppExceptionView::$err = $e;
        AppExceptionView::html();
        
        if( isset($this->errHandler[ $e->getCode() ]) ){
            $this->errHandler[ $e->getCode ]( $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
        }
        
        try {
        	$this->hook('\Sepbin\System\Core\IApplicationHook', 'applicationException', InstanceSet::CALL_VOID, $e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine() );
        }catch ( \Exception $e){
        	
        }
    }
    
    
}