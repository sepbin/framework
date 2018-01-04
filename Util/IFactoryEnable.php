<?php
namespace Sepbin\System\Util;

interface IFactoryEnable
{
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR );
		
	
	/**
	 * 初始化，用于代替__construct方法
	 * 使用_init方法来根据配置初始化实例
	 */
	public function _init( \Sepbin\System\Util\FactoryConfig $config );
	
}