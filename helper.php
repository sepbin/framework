<?php
use Sepbin\System\Util\ConfigUtil;
use Sepbin\System\Core\Application;
use Sepbin\System\Core\RequestParam;
use Sepbin\System\Http\HttpResponse;
use Sepbin\System\Http\Cookie;
use Sepbin\System\Http\Session;
use Sepbin\System\Cache\TempFile;


/**
 * 获取application实例
 * @return \Sepbin\System\Core\Application
 */
function getApp() : Application{
    
    $appName = config()->getStr('app_instance',\Sepbin\System\Core\Application::class);
    
    $instance = $appName::getInstance('application');
    
    return $instance;
    
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
 * @return \Sepbin\System\Core\RequestParam
 */
function request() : RequestParam{
    
    return getApp()->getRequest()->param;
    
}


/**
 * 获取http响应单例
 * @return \Sepbin\System\Http\HttpResponse
 */
function getHttp() : HttpResponse{
	
	return HttpResponse::getInstance('http');
	
}


/**
 * 获取Cookie单例
 * @return \Sepbin\System\Http\Cookie
 */
function getCookie(){
	return Cookie::getInstance('cookie');
}


/**
 * 获取Session单例
 * @return \Sepbin\System\Http\Session
 */
function getSession(){
    
	return Session::getInstance('session');
	
}


/**
 * 获取临时文件管理单例
 * @return \Sepbin\System\Cache\TempFile
 */
function getTemp(){
    
    return TempFile::getInstance('temp');
    
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
 * @param string $message
 * @param string $domain
 * @param mixed ...$params
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