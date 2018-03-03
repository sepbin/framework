<?php
namespace Sepbin\System\Service;


use Sepbin\System\Util\FileUtil;
use Sepbin\System\Util\ConsoleUtil;

/**
 * @desc clean project
 * @author joson
 *
 */
class Clean
{
	
	public function doAction(){
		
		$this->clearTemplateCache();
		ConsoleUtil::writeLine( 'done!' );
		
	}
	
	private function clearTemplateCache(){
		
		ConsoleUtil::writeLine( 'clearing template cache....' );
		$dir = DOCUMENT_ROOT.'/data/template';
		FileUtil::rmdir($dir);
		
	}
	
	
}