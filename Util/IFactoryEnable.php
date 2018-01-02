<?php
namespace Sepbin\System\Util;

interface IFactoryEnable
{
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR );
	
	static public function _factory( \Sepbin\System\Util\FactoryConfig $config ) : IFactoryEnable;
	
	
	/**
	 * 初始化，用于代替__construct方法
	 * __construct方法在new后就会执行
	 * _factory中，需要先实例化对象，才能设置属性
	 * 但是可能在构造方法中，需要用到设置的属性来进行初始化
	 * 使用_init方法来初始化，会在属性设置完成之后
	 */
	public function _init();
	
}