<?php
namespace Sepbin\System\Frame\Hook;

interface IMvcTemplateAdvHook
{
    
    /**
     * 是否允许模板继承
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function allowExtends( string $module, string $controller, string $action ):bool;
    
}