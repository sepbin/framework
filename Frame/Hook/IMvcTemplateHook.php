<?php
namespace Sepbin\System\Frame\Hook;

interface IMvcTemplateHook
{
	
	
	/**
	 * 模板管理器初始化时
	 * @param \Sepbin\System\Mvc\View\TemplateManager $manager
	 */
	public function tplManagerInit( \Sepbin\System\Frame\Mvc\View\TemplateManager $manager ) : void;
	
	/**
	 * 这个方法是在缓存视图之前触发
	 * 比如我们可以在这里实现网页内容的压缩等，或者其他的一些替换、增加内容处理
	 * 这个方法是安全的，因为结果将会被缓存，而无需过多担心效率
	 * @param string $content
	 * @return string
	 */
	public function tplCacheBefore( string $content ) : string;
	
}