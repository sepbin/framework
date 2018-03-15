<?php
use Sepbin\System\Util\ConfigUtil;


//项目根路径
if( !defined('DOCUMENT_ROOT')) define('DOCUMENT_ROOT', dirname(dirname(dirname(__DIR__))) );


//对HTTP公开资源的目录路径
if( !defined('PUBLIC_DIR') ) define('PUBLIC_DIR', DOCUMENT_ROOT.'/public');


//项目应用路径
if(!defined('APP_DIR')) define('APP_DIR', DOCUMENT_ROOT.'/application');


//项目类库路径
if(!defined('LIB_DIR')) define('LIB_DIR', DOCUMENT_ROOT.'/lib');


//项目配置文件路径
if(!defined('CONFIG_DIR')) define('CONFIG_DIR', DOCUMENT_ROOT.'/config');


//项目数据储存目录，需要有写权限
if(!defined('DATA_DIR')) define('DATA_DIR', DOCUMENT_ROOT.'/data');


//项目类库顶级命名
if(!defined('LIB_PREFIX')) define('LIB_PREFIX', 'SepLib\\');

//项目应用顶级命名
if(!defined('APP_PREFIX')) define('APP_PREFIX', 'SepApp\\');

if(!defined('APPLICATION_CONFIG')) define('APPLICATION_CONFIG', 'application.php');

//获取http根路径
$_findHttpRoot = function($path) use (&$_findHttpRoot){
    
    if(  $path == substr($_SERVER['REQUEST_URI'], 0, strlen($path)) || $path == '' ){
	    return $path;
	}else{
	    $dot = strpos($path, '/',1);
	    if( $dot !== false ){
		   $path = substr($path, $dot );
	    }else{
	       $path = ''; 
	    }
		return $_findHttpRoot( $path );
	}
};

$_appendPublicPath = function( $path ){
    if( $path == '' ) return '';
    $check = substr($_SERVER['SCRIPT_FILENAME'], strlen(DOCUMENT_ROOT));
    if( $check == '/public/index.php' ){
        return $path.'/public';
    }
    
};

if( !empty($_SERVER['REQUEST_URI']) ){
	define('HTTP_ROOT', $_appendPublicPath($_findHttpRoot(DOCUMENT_ROOT)) );
}else{
    define('HTTP_ROOT', '');
}


//--------------------------------------

include DOCUMENT_ROOT.'/vendor/autoload.php';

$config = ConfigUtil::getInstance();
$config->addPhpFile( APPLICATION_CONFIG );


include __DIR__.'/helper.php';


_registerLib(LIB_PREFIX, LIB_DIR);
_registerLib(APP_PREFIX, APP_DIR);

