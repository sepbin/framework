<?php
namespace Sepbin\System\Mvc\View;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\StringUtil;
use Sepbin\System\Mvc\Exception\TemplateFileNoFoundException;

class Template extends Base
{
	
	public $extension = 'html';
	
	public $parseEngine = 'Sepbin\System\Mvc\View\Template\ArtTemplate';
	
	private $filename;
	
	
	private $data;
	
	
	function __construct( string $controller_name, string $action_name, array $data ){
		
		
		$this->filename = $this->getFilename($controller_name, $action_name);
		
		$this->data = $data;
		
	}
	
	
	private function getFilename( string $controller_name, string $action_name ){
		
		$controller_name = ltrim($controller_name,'SepApp\Application');
		$controller_name = rtrim($controller_name,'Controller');
		
		$filename = APP_DIR.'/View/'.$controller_name.'/'.StringUtil::camelToUnderline($action_name).'.'.$this->extension;
		$filename = str_replace('\\', '/', $filename);
		
		return $filename;
		
	}
	
	
	public function getContent(){
		
		if(!file_exists($this->filename)){
			
			throw (new TemplateFileNoFoundException())->appendMsg( $this->filename );
			
		}
		
		$content = file_get_contents($this->filename);
		$tempEngine = new $this->parseEngine( $content );
		
		return $tempEngine->parse();
		
	}
	
	
	
}


abstract class AbsTemplate
{
	
	protected $content;
	
	function __construct( string $content ){
		$this->content = $content;
	}
	
	abstract public function parse():string;
	
}