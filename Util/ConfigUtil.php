<?php
namespace Sepbin\System\Util;

use Sepbin\System\Util\Data\DotName;
use Sepbin\System\Util\Traits\TGetType;

class ConfigUtil
{
	
	use TGetType;
	
	private $config = array();
	
	private $loadedFilename = array();
	
	
	static public function getInstance() : ConfigUtil{
		
		static $instance = null;
		
		if( $instance == null ){
			$instance = new ConfigUtil();
		}
		
		return $instance;
		
	}
	
	
	public function addIniFile( string $filename, string $path = CONFIG_DIR ){
		
		$fullname = FileUtil::combineFullName($filename, $path);
		
		if( $this->checkLoadedFile($fullname) ) return ;
		
		$content = $this->loadFile( $fullname );
		
		if(empty($content)) return ;
		
		$config = parse_ini_string($content,true);
		
		foreach ( $config as $key=>$val ){
			if( strpos($key, '.') ){
				DotName::set($this->config, $key, $val);
			}else{
				$this->config[$key] = $val;
			}
		
		}
		
	}
	
	
	
	
	public function addPhpFile( string $filename, string $path = CONFIG_DIR ){
		
		$fullname = FileUtil::combineFullName($filename, $path);
		
		if( $this->checkLoadedFile($fullname) ) return ;
		
		$config = $this->loadFile($fullname);
		if(empty($config)) return ;
		
		foreach ( $config as $key=>$val ){
			
			$this->config[$key] = $val;
			
		}
		
	}
	
	public function addXmlFile( string $filename, string $path = CONFIG_DIR ){
		
		$fullname = FileUtil::combineFullName($filename, $path);
		
		if( $this->checkLoadedFile($fullname) ) return ;
		
		$content = $this->loadFile( $fullname );
		
		if(empty($content)) return ;
		
		$content = \simplexml_load_string($content);
		
		$config = json_decode( json_encode( $content ), true );
		
		foreach ( $config as $key=>$val ){
			
			$this->config[$key] = $val;
			
		}
		
		
	}
	
	public function addJsonFile( string $filename, string $path = CONFIG_DIR ){
		
		$fullname = FileUtil::combineFullName($filename, $path);
		
		if( $this->checkLoadedFile($fullname) ) return ;
		
		$content = $this->loadFile( $fullname );
		if(empty($content)) return ;
		$config = json_decode($content,true);
		
		foreach ( $config as $key=>$val ){
			
			$this->config[$key] = $val;
			
		}
		
	}
	
	public function addFile( string $filename, string $path = CONFIG_DIR ){
		
		$ext = FileUtil::getExtensionName($filename);
		
		if( $ext == 'php' ){
			$this->addPhpFile($filename,$path);
		}
		
		if( $ext == 'json' ){
			$this->addJsonFile($filename,$path);
		}
		
		if( $ext == 'xml' ){
			$this->addXmlFile($filename,$path);
		}
		
		if( $ext == 'ini' ){
			$this->addIniFile($filename,$path);
		}
		
	}
	
	
	private function loadFile( string $fullname ){
		
		
		if( !file_exists($fullname) ){
			//throw (new ConfigFileNotFindException())->appendMsg( $fullname );
			trigger_error('配置文件没有找到：'.$fullname, E_USER_WARNING);
			return null;
		}
		
		$this->loadedFilename[ $fullname ] = true;
		
		if( FileUtil::getExtensionName($fullname) == 'php' ){
			
			return include $fullname;
			
		}else{
			
			return file_get_contents($fullname);
			
		}
		
	}
	
	public function checkLoadedFile( string $fullname ) : bool{
		
		if( isset($this->loadedFilename[$fullname]) ){
			return true;
		}
		
		return false;
		
	}
	
	public function get( string $name, $default='' ){
		
		return DotName::get($this->config,$name,$default);
		
		
	}
	
	
	/**
	 * 设置一项配置
	 * @param string $name 用点语法表示
	 * @param mixed $value
	 */
	public function set( string $name, $value ){
		
		DotName::set($this->config,$name, $value);
		
	}
	
	
	/**
	 * 检查配置名是否存在
	 * @param string $name 用点语法表示 如配置为 'a' => ['b'=>[]]，则用 a.b表示
	 * @return boolean  返回true为存在，返回false为不存在
	 */
	public function check( string $name ) : bool{
		
		$d = DotName::get($this->config,$name,null);
		
		if($d != null) return true;
		
		return false;
		
	}
	
	/**
	 * 检查配置名是否是另一个配置的指针
	 * 有些子类可以允许单例，在配置中可以指向一个公共的配置
	 * 但如果希望每个子类都是单独的实例，则可用此方法判断
	 * @param string $name
	 * @return bool 返回false为配置不存在或不是一个指针，返回true为配置是一个指针
	 */
	public function checkPointer( string $name ) : bool{
	    
	    $d = DotName::get($this->config,$name,null);
	    if($d == null) return false;
	    
	    if( !is_string($d) ) return false;
	    return true;
	    
	}
	
	
}