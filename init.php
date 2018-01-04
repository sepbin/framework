<?php
use Sepbin\System\Util\ConfigUtil;
use Sepbin\System\Core\Application;

//项目根路径
if(!defined('DOCUMENT_ROOT')) define('DOCUMENT_ROOT', dirname(get_included_files()[0]));


//项目应用路径
if(!defined('APP_DIR')) define('APP_DIR', DOCUMENT_ROOT.'/application');


//项目类库路径
if(!defined('LIB_DIR')) define('LIB_DIR', DOCUMENT_ROOT.'/lib');


//项目配置文件路径
if(!defined('CONFIG_DIR')) define('CONFIG_DIR', DOCUMENT_ROOT.'/config');


//项目类库顶级命名
if(!defined('LIB_PREFIX')) define('LIB_PREFIX', 'SepLib\\');

//项目应用顶级命名
if(!defined('APP_PREFIX')) define('APP_PREFIX', 'SepApp\\');


//获取http根路径
function _findHttpRoot($path){
	if( $path == substr($_SERVER['REQUEST_URI'], 0, strlen($path)) || $path == '' ){
		return $path;
	}else{
		$path = substr($path, strpos($path, '/',1));
		return _findHttpRoot( $path );
	}
}
if( !empty($_SERVER['REQUEST_URI']) ){
	define('HTTP_ROOT', _findHttpRoot(DOCUMENT_ROOT));
}else{
	define('HTTP_ROOT', '');
}
//--------------------------------------



/**
 * 获取application实例
 * @return \Sepbin\System\Core\Application
 */
function getApp(){
    return \Sepbin\System\Core\Application::getInstance('application','application.ini');
}


/**
 * 获取配置实例
 * @return \Sepbin\System\Util\ConfigUtil
 */
function config(){
	return ConfigUtil::getInstance();
}


/**
 * 获取请求参数实例
 * @param Application $app
 * @return \Sepbin\System\Http\RequestParam
 */
function request( Application $app = null ){
	if( $app == null ){
		return getApp()->getRequest()->param;
	}
	return $app->getRequest()->param;
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


//自动加载类文件
$loader = include 'vendor/autoload.php';
function _registerLib( $namespace_pre, $dir ){
	global $loader;
	$loader->addPsr4($namespace_pre, $dir);
}
_registerLib(LIB_PREFIX, LIB_DIR);
_registerLib(APP_PREFIX, APP_DIR);
