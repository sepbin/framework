<?php
namespace Sepbin\System\Core;

use Sepbin\System\Http\Request;
use Sepbin\System\Http\Response;

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
    
    
    static function getInstance(){
        
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
    public function registerHook( string $name, string $hook_name, string $method ){
        
    	$this->hook[ $name ] = array(
    		'name' => $hook_name,
    		'method' => $method
    	);
    	
    }
    
    
    /**
     * 注册服务
     */
    public function registerService(){
         
    }
    
    
    /**
     * 注册错误钩子
     * @param unknown $errorCode
     * @param \Closure $func
     */
    public function registerErrorHandler( $error_code, \Closure $func ){
        
        $this->errHandler[ $error_code ] = $func;
        
    }
    
    /**
     * 运行过程
     * @param \Closure $func  运行的匿名函数
     */
    public function process( \Closure $func ){
        
        $this->process[] = $func;
        
    }
    
    /**
     * 开始运行
     */
    public function run(){
        
        if( !empty($this->process) ){
            foreach ($this->process as $item){
                $item();
            }
        }
        
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
    public function getRequest(){
    	return $this->request;
    }
    
    
    /**
     * 获取应用响应对象
     * @return \Sepbin\Http\Response
     */
    public function getResponse(){
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
        
    }
    
    
}