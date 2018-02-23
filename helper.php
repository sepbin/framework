<?php
use Sepbin\System\Util\ConfigUtil;
use Sepbin\System\Core\Application;
use Sepbin\System\Core\RequestParam;

/**
 * 获取application实例
 * @return \Sepbin\System\Core\Application
 */
function getApp() : Application{
    $appName = config()->getStr('app_instance',\Sepbin\System\Core\Application::class);
    return $appName::getInstance('application','application.php');
}


/**
 * 获取配置实例
 * @return \Sepbin\System\Util\ConfigUtil
 */
function config() : ConfigUtil{
    
    return ConfigUtil::getInstance();
    
}


/**
 * 获取请求参数实例
 * @param Application $app
 * @return \Sepbin\System\Http\RequestParam
 */
function request() : RequestParam{
    
    return getApp()->getRequest()->param;
    
}



/**
 * 输出
 * @param unknown $data
 */
function dump( $data ){
    
    getApp()->getResponse()->put($data);
    
}


/**
 * 获取语言
 * @param unknown $message
 * @param unknown $domain
 * @param unknown ...$params
 * @return string
 */
function _lang( $message, $domain, ...$params ){
    
    return sprintf( dgettext($domain, $message), ...$params );
    
}


/**
 * 获取Application.mo包语言
 * @param string $message
 * @param array $data
 * @return string
 */
function _t( string $message, array $data=array() ):string{
    
    return _lang($message, 'Application', ...$data);
    
}


/**
 * 获取URL路径
 * @param unknown $url
 * @return string|unknown
 */
function _url( $url ){
    if( !getApp()->httpRewrite ){
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/index.php'.HTTP_ROOT;
    }else{
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].HTTP_ROOT;
    }
    return $url;
}


/**
 * 注册类库
 * @param string $namespace_pre     命名空间前缀
 * @param string $dir               类库所在目录
 */
function _registerLib( $namespace_pre, $dir ){
    
    $loader = include DOCUMENT_ROOT.'/vendor/autoload.php';
    $loader->addPsr4($namespace_pre, $dir);
    
}