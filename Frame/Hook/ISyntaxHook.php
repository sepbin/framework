<?php
namespace Sepbin\System\Frame\Hook;

use Sepbin\System\Frame\Mvc\View\BasicSyntax;

interface ISyntaxHook
{
    
    /**
     * 解析器初始化时
     * 可以通过这个接口更改解析器的一些参数，比如注册自定义的"宏"
     * @param BasicSyntax $syntax
     */
    public function init( BasicSyntax $syntax );
    
}