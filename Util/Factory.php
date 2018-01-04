<?php
namespace Sepbin\System\Util;

use Sepbin\System\Util\Exception\FactoryTypeException;

class Factory
{
	
	static private $scheme = array();
	
	static public function get( $name, string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ) {
		
		
		if( $config_file != null ){
			
			ConfigUtil::getInstance()->addFile( $config_file, $config_path );
			
		}
		
		
		if( $config_namespace == null ){
			
			$config_namespace = '____default____';
			
		}
		
		
		
		if( !isset( self::$scheme[ $name ][ $config_namespace ] ) ){
			
			if( !ConfigUtil::getInstance()->checkNamespace($config_namespace) ){
				trigger_error('代码中声明却缺少命名空间为'.$config_namespace.'的配置',E_USER_WARNING);
			}
			
			$config = new FactoryConfig( ConfigUtil::getInstance()->getNamespace($config_namespace) );
			
			
			self::$scheme[ $name ][ $config_namespace ] = new $name();
			
			if( !self::$scheme[ $name ][ $config_namespace ] instanceof IFactoryEnable ){
				throw (new FactoryTypeException())->appendMsg( $name );
			}
			
			self::$scheme[ $name ][ $config_namespace ]->_init($config);
			
			
		}
		
		return self::$scheme[ $name ][ $config_namespace ];
			
		
	}
	
	
	static public function getForString( $condition ){
		
		$tmp = explode(':', $condition);
		if( count($tmp) == 1 ){
			return self::get($tmp[0]);
		}
		
		if( count($tmp) == 2 ){
			return self::get($tmp[0], $tmp[1]);
		}
		
		if( count($tmp) == 3 ){
			return self::get($tmp[0], $tmp[1], $tmp[2]);
		}
		
	}
	
}