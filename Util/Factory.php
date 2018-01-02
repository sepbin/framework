<?php
namespace Sepbin\System\Util;

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
			
			$config = new FactoryConfig( ConfigUtil::getInstance()->getNamespace($config_namespace) );
			
			self::$scheme[ $name ][ $config_namespace ] = $name::_factory( $config );
			self::$scheme[ $name ][ $config_namespace ]->_init();
			
			
		}
		
		return self::$scheme[ $name ][ $config_namespace ];
			
		
		
	}
	
}