<?php
namespace Sepbin\Core;

/**
 * 应用入口
 * @author joson
 *
 */
class Application extends Base
{
    
    
    
    static function getInstance(){
        
        static $instance = null;
        
        if( $instance == null ){
            $instance = new Application();
        }
        
        return $instance;
        
    }
    
    function __construct(){
        
        set_error_handler(array($this,'error'));
        
        set_exception_handler(array($this,'exception'));
        
    }
    
    /**
     * 注册钩子
     */
    public function registerHook(){
        
    }
    
    /**
     * 注册服务
     */
    public function registerService(){
            
    }
    
    /**
     * 运行过程
     * @param \Closure $func  运行的匿名函数
     */
    public function process( \Closure $func ){
        
    }
    
    /**
     * 开始运行
     */
    public function run(){
        
    }
    
    /**
     * 脚本错误
     */
    private function error(){
        
    }
    
    /**
     * 应用异常
     */
    private function exception(){
        
    }
    
}