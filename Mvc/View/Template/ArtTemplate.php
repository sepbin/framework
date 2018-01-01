<?php
namespace Sepbin\System\Mvc\View\Template;

use Sepbin\System\Mvc\View\AbsTemplate;

class ArtTemplate extends AbsTemplate
{
		
	public function parse():string{
		
		return $this->content;
		
	}
	
}