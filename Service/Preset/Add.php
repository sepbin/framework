<?php
namespace Sepbin\System\Service\Preset;



use Sepbin\System\Util\ConsoleUtil;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Service\AbsService;

/**
 * @desc add project parts
 * @author joson
 *
 */
class Add extends AbsService
{
	
	
	public function controllerAction(){
		
		ConsoleUtil::writeLine('input your module name, such as "my_module"');
		$module = ConsoleUtil::getRequireInput('module name?');
		
		
		ConsoleUtil::writeLine('input your controller name, such as "my_controller"');
		
		$root = DOCUMENT_ROOT.'/application/Application';
		$filename = '';
		$className = '';
		
		$controller = ConsoleUtil::getRequireInput('controller name?',[], function($answer) use (&$filename,&$className,$root){
			
			$className = ClassName::underlineToCamel($answer,true).'Controller';
			$filename = $root.'/'.ClassName::underlineToCamel($module,true).'/'.$className.'.php';
			
		});
		
		
		
		
		
	}
	
	
	public function serviceAction(){
		
		ConsoleUtil::writeLine('input your service name, such as "my_test"');
		
		
		$className = null;
		$filename = null;
		
		$name = ConsoleUtil::getRequireInput('service name?',[],function($answer) use (&$className,&$filename){
			
			$className = ClassName::underlineToCamel($answer, true);
			$filename = DOCUMENT_ROOT.'/application/Service/'.$className.'.php';
			
			if( file_exists( $filename ) ){
				ConsoleUtil::writeError('error : '.$answer.' already exist');
				return false;
			}
			
			return true;
			
		});
		
		ConsoleUtil::writeLine('input '.$name.' description.');
		$description = ConsoleUtil::getInput('description?');
		
		
		$actions = [];
		
		ConsoleUtil::writeLine('you can add command, such as "start". if input blank will end');
		while ( '' != ($action = ConsoleUtil::getInput('add command?')) ){
			if( !in_array($action, $actions) ){
				$actions[] = $action;
			}
			
			ConsoleUtil::writeLine('already {'.implode('|', $actions).'}');
			
		}
		
		
		$content ='<?php
namespace SepApp\Service;
/**
*
*@desc '.$description.'
*
*/
class '.$className.'
{
		
	public function doAction(){
		
	}';
		
		foreach ($actions as $item){
			$content.='
					
	public function '.$item.'Action(){
			
	}
';
		}
		
		
		$content.= '

}';
		
		ConsoleUtil::writeSuccess('write '.$filename);
		file_put_contents($filename, $content);
		ConsoleUtil::writeSuccess('done!');
		
	}
	
	
	
}