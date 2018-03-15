<?php
namespace Sepbin\System\Service\Preset;


use Sepbin\System\Util\FileUtil;
use Sepbin\System\Util\ConsoleUtil;
use Sepbin\System\Service\AbsService;
use Sepbin\System\Cache\TempFile;

/**
 * @desc clean project
 * @author joson
 *
 */
class Clean extends AbsService
{
	
	public function doAction(){
		
		$this->clearTemplateCache();
		$this->clearTempPath();
		ConsoleUtil::writeLine( 'done!' );
		
	}
	
	private function clearTemplateCache(){
		
		ConsoleUtil::writeLine( 'clearing template cache....' );
		$dir = DOCUMENT_ROOT.'/data/template';
		FileUtil::rmdir($dir,function($file,$result){
		    if( $result ){
		        ConsoleUtil::writeSuccess( 'delete ' . $file );
		    }else{
		        ConsoleUtil::writeError( 'Permission denied ' . $file );
		    }
		    
		    usleep(30000);
		});
		
	}
	
	/**
	 * 清理临时存取目录
	 */
	private function clearTempPath(){
	    
	    ConsoleUtil::writeLine( 'clearing temp cache....' );
	    $temp = TempFile::getInstance();
	    $temp->clean();
	    
	}
	
	
}