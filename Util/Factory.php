<?php
namespace Sepbin\System\Util;

use Sepbin\System\Util\Exception\FactoryTypeException;
use Sepbin\System\Core\SepException;

class Factory
{
	
	static private $scheme = array();
	
	/**
	 * 生产一个实例
	 * @param string $name   实例名称
	 * @param string $config_namespace  配置的域名
	 * @param string $config_file	配置文件名称
	 * @param string $config_path	配置文件的路径 默认为CONFIG_DIR常量
	 * @return mixed
	 */
	static public function get( string $name, string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ) {
		
		if( $config_file != null ){
			
			ConfigUtil::getInstance()->addFile( $config_file, $config_path );
			
		}
		
		
		if( $config_namespace == null ){
			
			$config_namespace = '____default____';
			
		}
		
		
		
		if( !isset( self::$scheme[ $name ][ $config_namespace ] ) ){
			
			if( $config_namespace != '____default____' && !ConfigUtil::getInstance()->check($config_namespace) ){
				trigger_error('代码中声明却缺少命名空间为'.$config_namespace.'的配置',E_USER_WARNING);
			}
			
			$config = new FactoryConfig( $config_namespace ,ConfigUtil::getInstance()->get($config_namespace, array()) );
			$config->file = $config_file;
			$config->filePath = $config_path;
			
			self::$scheme[ $name ][ $config_namespace ] = new $name();
			
			if( !self::$scheme[ $name ][ $config_namespace ] instanceof IFactoryEnable ){
				throw (new FactoryTypeException())->appendMsg( $name );
			}
			
			self::$scheme[ $name ][ $config_namespace ]->_init($config);
			
		}
		
		return self::$scheme[ $name ][ $config_namespace ];
			
		
	}
	
	
	/**
	 * 销毁一个实例
	 * @param string $name
	 * @param string $config_namespace
	 */
	static public function destroy( string $name, string $config_namespace='' ){
		
		if( $config_namespace == null ){
			$config_namespace = '____default____';
		}
		
		unset( self::$scheme[$name][$config_namespace] );
		
	}
	
	
	/**
	 * 根据字符串获取实例，每个参数
	 * @example 如 sepbin\appliction:app:app.ini
	 * @param unknown $condition
	 * @return mixed
	 */
	static public function getForString( string $condition ){
		
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
		
		throw (new SepException())->appendMsg( $condition );
		
	}
	
}