<?php
namespace Sepbin\System\Util;

interface IFactoryEnable
{
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR );
	
	static public function _factory( FactoryConfig $config ) : IFactoryEnable;
	
}