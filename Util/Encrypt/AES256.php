<?php
namespace Sepbin\System\Util\Encrypt;

use Sepbin\System\Util\Factory;

class AES256 implements IEncrypt
{
	
	
	private $key;
	
	private $iv;
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):AES256{
		
		return Factory::get(AES256::class, $config_namespace, $config_file, $config_path);
		
	}
	
	
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		$this->key = $config->get('key','qtjrljsdfkln32n4j89sdfgjkaw89df7345jkhklsadf89734534345sadfhjkl');
		$this->iv = substr($config->get('iv','dsds439skcmslq23'), 0, 16 );
		
	}
	
	
	public function encrypt( string $data ):string{
		
		return $str = openssl_encrypt($data, 'AES-256-CBC', $this->key,0,$this->iv);
		
	}
	
	public function decrypt( string $data ):string{
		
		return openssl_decrypt($data, 'AES-256-CBC', $this->key,0,$this->iv);
		
	}
	
}