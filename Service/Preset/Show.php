<?php
namespace Sepbin\System\Service;

use Sepbin\System\Util\FileUtil;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Util\ConsoleUtil;

/**
 * 
 * @desc show all service
 * @author joson
 *
 */
class Show
{
	
	public function doAction(){
		
		
		$this->writeHead();
		
		$param = false;
		
		if( request()->getBool('project', false) || request()->getBool('p',false) ){
			
			$this->writeCommand(DOCUMENT_ROOT.'/application/Service');
			$param = true;
			
		}
		
		
		if( request()->getBool('sepbin', false) || request()->getBool('s',false) ){
			
			$this->writeCommand(__DIR__);
			$param = true;
			
		}
		
		if( !$param ){
			$this->writeCommand(__DIR__);
			$this->writeCommand(DOCUMENT_ROOT.'/application/Service');
		}
		
		
	}
	
	public function projectAction(){
		
		$this->writeHead();
		$this->writeCommand(DOCUMENT_ROOT.'/application/Service');
		
	}
	
	public function defaultAction(){
		
		$this->writeHead();
		$this->writeCommand(__DIR__);
		
	}
	
	private function writeHead(){
		ConsoleUtil::writeHorizontal();
		ConsoleUtil::write(  ConsoleUtil::text('name', 4, ConsoleUtil::COLOR_BLACK, ConsoleUtil::COLOR_WHITE)  );
		ConsoleUtil::write( ConsoleUtil::text('',16) );
		ConsoleUtil::write(  ConsoleUtil::text('description', -1, ConsoleUtil::COLOR_BLACK, ConsoleUtil::COLOR_WHITE)  );
		ConsoleUtil::writeEnter();
	}
	
	
	private function writeCommand( $dirname ){
		
		$dir = dir($dirname);
		while ( false !== ($file = $dir->read()) ){
			
			if($file != '.' && $file != '..'){
				
				$name = ClassName::camelToUnderline( FileUtil::getName($file) );
				ConsoleUtil::write( ConsoleUtil::text($name,20, ConsoleUtil::COLOR_BLUE) );
				ConsoleUtil::write( $this->getNote( $dirname.'/'.$file ) );
				ConsoleUtil::writeEnter();
				
			}
			
		}
		
	}
	
	
	private function getNote( $filename ){
		
		$content = file_get_contents($filename);
		
		preg_match('/\/\*\*(.+?)\*\//is', $content, $matches);
		
		if(!empty($matches) && !empty($matches[1])){
			$content = $matches[1];
			$content = str_replace('*', '', $content);
			preg_match('/@desc ([^@]+)/is', $content, $matches);
			
			if(!empty($matches) && !empty($matches[1])){
				return trim( $matches[1] );
			}
			
		}
		
		return '';
		
	}
	
}