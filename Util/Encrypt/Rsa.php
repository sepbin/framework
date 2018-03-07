<?php
namespace Sepbin\System\Util\Encrypt;

use Sepbin\System\Util\Factory;

class Rsa implements IEncrypt
{
	
	
	private $public_key;
	
	private $private_key;
	
	private $public_key_resource;
	
	private $private_key_resource;
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):RSA{
		
		return Factory::get(RSA::class, $config_namespace, $config_file, $config_path);
		
	}
	
	
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		$this->public_key = $config->getStr('public_key');
		
		$this->private_key = $config->getStr('private_key');
		
		if( $config->getStr('public_key_file') != '' ){
			
			$this->public_key = file_get_contents( $config->getStr('public_key_file') );
			
		}
		
		if( $config->getStr('private_key_file') != '' ){
			
			$this->private_key = file_get_contents( $config->getStr('private_key_file') );
			
		}
		
	}
	
	
	public function encrypt( string $data ):string{
		
		
		if( empty( $this->private_key ) ) return '';
		if( $this->private_key_resource == null ){
			$privateKey = openssl_pkey_get_private( $this->private_key );
			if( !$privateKey ) return '';
			$this->private_key_resource = $privateKey;
		}
		
		if( !openssl_private_encrypt($data, $crypted, $this->private_key_resource) ) return '';
		
		return base64_encode( $crypted );
		
	}
	
	public function decrypt( string $data ):string{
		
		if( empty($data) ) return '';
		if( empty($this->public_key) ) return '';
		if( $this->public_key_resource == null ){
			$publicKey = openssl_pkey_get_public($this->public_key);
			if( !$publicKey ) return '';
			$this->public_key_resource = $publicKey;
		}
		
		$crypted = base64_decode($data);
		if( empty($crypted) ) return '';
		if( !openssl_public_decrypt($crypted, $decrypted, $this->public_key_resource) ) return '';
		
		return $decrypted;
		
	}
	
}