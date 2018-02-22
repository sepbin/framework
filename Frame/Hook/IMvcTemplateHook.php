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
	
	
	/**
	 * 模板内容显示之前
	 * 这个接口可以拦截模板的输出，能够更改包括数据库读取的内容在内的所有内容
	 * 建议不要在此接口中做需要大规模运算的工作，因为每次输出都会运行此方法
	 * @param string $content
	 * @return string
	 */
	public function tplViewBefore( string $content ): string;
	
	
	
	
}