<?php
namespace Sepbin\System\Frame\Hook;


interface IMvcTemplateObjectHook
{
	
    /**
     * 模板对象初始化时
     * 一些模板的公共变量可以在此设定，也可以在此更改变量的值
     * @param TemplateObject $tpl
     */
    public function tplObjectInit( \Sepbin\System\Frame\Mvc\View\TemplateObject $tpl ):void;
    
    
	
}