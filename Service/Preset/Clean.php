<?php
namespace Sepbin\System\Service\Preset;


use Sepbin\System\Util\FileUtil;
use Sepbin\System\Util\ConsoleUtil;
use Sepbin\System\Service\AbsService;

/**
 * @desc clean project
 * @author joson
 *
 */
class Clean extends AbsService
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