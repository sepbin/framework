<?php
namespace Sepbin\System\Frame\Mvc\View;

use Sepbin\System\Core\Base;

abstract class AbsMacro extends Base
{
    
    /**
     * 
     * @var TemplateManager
     */
    protected $manager;
    
    function __construct( TemplateManager $manager ){
        
        $this->manager = $manager;
        
    }
    
}