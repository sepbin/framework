<?php
namespace Sepbin\System\Util\Encrypt;

use Sepbin\System\Util\Factory;

class DesEde3 implements IEncrypt
{
	
	
	private $key;
	
	private $iv;
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):DESede3{
		
		return Factory::get(DESede3::class, $config_namespace, $config_file, $config_path);
		
	}
	
	
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		$this->key = $config->get('key','qtjrljsdfkln32n4j89sdfgjkaw89df7345jkhklsadf89734534345sadfhjkl');
		$this->iv = substr($config->get('iv','dsds439skcmslq23'), 0, 8 );
		
	}
	
	
	public function encrypt( string $data ):string{
		
		return $str = openssl_encrypt($data, 'des-ede3-cbc', $this->key,0,$this->iv);
		
	}
	
	public function decrypt( string $data ):string{
		
		return openssl_decrypt($data, 'des-ede3-cbc', $this->key,0,$this->iv);
		
	}
	
}