<?php
namespace Sepbin\System\Mvc;

interface IMvcHook
{
	
	
	
	
	
	/**
	 * 模型渲染之前执行的
	 * 比如使用此hook，在模型渲染之前，还有机会更改模型的自动渲染类型
	 * 如：$model->setAutoDataType( Model::XML )
	 * 或者附加信息等
	 * @param Model $model
	 */
	public function modelRenderBefore( Model $model );
	
}