<?php
namespace Sepbin\System\Frame\Hook;

interface IMvcRenderHook
{
	
	/**
	 * 渲染器创建之前
	 * 这个Hook将允许更改render类型，框架的AutoController就是通过此接口将渲染器换成autoViewRender
	 * 
	 * @param string $render_name 将要使用的渲染器名称
	 * @param \Sepbin\System\Mvc\AbsController $controller 将要执行的控制器实例
	 * @param string $action  执行的方法名称
	 * @return string 更改的渲染器名称
	 */
	public function renderCreateBefore( string $render_name, \Sepbin\System\Frame\AbsController $controller, string $action ) : string;
	
}