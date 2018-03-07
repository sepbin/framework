<?php
namespace Sepbin\System\Cache;

use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Cache\Temp\LocaleFile;
use Sepbin\System\Cache\Temp\ATemp;


/**
 * 临时文件
 * 和cache相比，TempFile基于文件系统
 * @author joson
 *
 */
class TempFile implements IFactoryEnable
{
    
    
    public $preName = '';
    
    public $expire = 0;
    
    /**
     * 
     * @var ATemp
     */
    public $driver;
    
    /**
     * 清除临时目录概率
     * 100为百分之一，150为一百五十分之一
     * @var integer
     */
    public $cleanTmpProbability;
    
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ):TempFile{
        
        
        return Factory::get(TempFile::class, $config_namespace, $config_file, $config_path);
        
        
    }
    
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        $this->preName = $config->getStr('pre','sep_');
        $this->expire = $config->getInt('expire',36000);
        $this->driver = $config->getClass('driver', LocaleFile::class, ATemp::class);
        $this->cleanTmpProbability = $config->getInt('clean_temp_probability',100);
        
        if( mt_rand(1, $this->cleanTmpProbability) == 1 ){
            $this->clean();
        }
        
    }
    
    
    /**
     * 创建一个临时文件
     */
    public function write( string $data, $ext='', $key='' ) : string{
        
        if( $key == '' ){
            $key = $this->driver->createName();
        }
        
        $key = $this->preName.$key;
        
        if( $ext != '' ){
            $key .= '.'.$ext;
        }
        
        $this->driver->write($key, $data);
        
        return $key;
        
    }
    
    public function getFilename( $key ) : string{
        
        return $this->driver->getDiskFilename($key);
        
    }
    
    
    public function read( $key ) : string{
        
        return $this->driver->read($key);
        
    }
    
    
    public function del( $key ){
        
        $this->driver->del($key);
        
    }
    
    
    /**
     * 清理临时文件
     */
    public function clean(){
        
        $this->driver->clean( $this->expire );
        
    }
    
    
}