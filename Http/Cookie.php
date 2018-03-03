<?php
namespace Sepbin\System\Http;


use Sepbin\System\Core\Base;
use Sepbin\System\Util\Traits\TGetType;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Util\Encrypt\AES256;
use Sepbin\System\Util\Encrypt\IEncrypt;

class Cookie extends Base implements IFactoryEnable
{
	
	use TGetType;
	
	public $prefix;
	
	public $expire;
	
	public $isEncrypt;
	
	public $encryptMethod;
	
	/**
	 * 
	 * @var IEncrypt
	 */
	private $encrypt;
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):Cookie{
		
		if( $config_namespace == null ){
			$config_namespace = 'cookie';
		}
		
		return Factory::get(Cookie::class, $config_namespace,$config_file,$config_path);
		
	}
	
	
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		$this->prefix = $config->getStr('prefix');
		$this->expire = $config->getInt('expire',1800);
		$this->isEncrypt = $config->getBool('is_encrypt',true);
		$this->encryptMethod = $config->getStr('encrypt', AES256::class);
		
		if( $this->isEncrypt ){
			$this->encrypt = $config->getInstance('encrypt', $this->encryptMethod);
		}
		
	}
	
	public function set( $name, $value, $expire=-1, $path='/', $domain=null, $secure=false ){	
		
		if( $expire == -1 ) $expire = $this->expire;
		$name = $this->prefix . $name;
		
		if( $expire != 0 ){
		  $expire = time() + $expire;
		}
		
		if( is_object($value) || is_array($value) ){
			$value = '[@'. base64_encode( json_encode( $value ) ).']';
		}
		
		if( $this->isEncrypt ){
			$value = $this->encrypt->encrypt($value);
		}
		
		setcookie($name,$value,$expire,$path,$domain,$secure);
		
	}
	
	public function del( $name, $path='/', $domain=null, $secure=false ){
		
		$name = $this->prefix . $name;
		setcookie($name,false,time()-1, $path, $domain, $secure);
		
	}
	
	public function get( string $name, $default='' ){
		
		$name = $this->prefix . $name;
		if( !isset($_COOKIE[$name]) ) return null;
		
		$value = $_COOKIE[$name];
		
		if($this->isEncrypt) $value = $this->encrypt->decrypt($value);
		
		
		if( substr($value, 0, 2) == '[@' && substr($value, strlen($value)-1) == ']' ){
			$value = substr($value, 2, strlen($value) -3 );
			$value = json_decode( base64_decode($value) );
		}
		
		return $value;
		
	}
	
	
	
	
}