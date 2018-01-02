<?php
namespace Sepbin\System\Mvc\Hook;

interface IMvcModelHook
{
	
	/**
	 * 模型渲染之前执行的
	 * 比如使用此hook，在模型渲染之前，还有机会更改模型内容
	 * @param Model $model
	 */
	public function modelRenderBefore( \Sepbin\System\Mvc\Model $model ) : void;
	
	
	/**
	 * 模型被创建时
	 * @param Model $model
	 */
	public function modelCreate( \Sepbin\System\Mvc\Model $model) : void;
	
}