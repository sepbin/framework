<?php
use Sepbin\System\Util\ConfigUtil;
use Sepbin\System\Core\Application;


/**
 * 项目根路径
 * @global
 * @var string
 */
if(!defined('DOCUMENT_ROOT')) define('DOCUMENT_ROOT', dirname(get_included_files()[0]));

if(!defined('APP_DIR')) define('APP_DIR', DOCUMENT_ROOT.'/application');

if(!defined('LIB_DIR')) define('LIB_DIR', DOCUMENT_ROOT.'/lib');

if(!defined('CONFIG_DIR')) define('CONFIG_DIR', DOCUMENT_ROOT.'/config');

if(!defined('LIB_PREFIX')) define('LIB_PREFIX', 'SepLib\\');

if(!defined('APP_PREFIX')) define('APP_PREFIX', 'SepApp\\');


//http根路径
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

function getApp(){
    
    return \Sepbin\System\Core\Application::getInstance('application','application.ini');
    
}

function config(){
	
	return ConfigUtil::getInstance();
	
}

function request( Application $app = null ){
	
	if( $app == null ){
		return getApp()->getRequest()->param;
	}
	
	return $app->getRequest()->param;
	
}

function dump( $data ){
	
	getApp()->getResponse()->put($data);
	
}

function _lang( $message, $domain, ...$params ){
	
	return sprintf( dgettext($domain, $message), ...$params );
	
}

function _t( string $message, array $data=array() ):string{
	
	return _lang($message, 'Application', ...$data);
	
}


$loader = include 'vendor/autoload.php';

function _registerLib( $namespace_pre, $dir ){
	global $loader;
	$loader->addPsr4($namespace_pre, $dir);
}

_registerLib(LIB_PREFIX, LIB_DIR);
_registerLib(APP_PREFIX, APP_DIR);
