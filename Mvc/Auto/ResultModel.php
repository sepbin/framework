<?php
namespace Sepbin\System\Mvc\Auto;


use Sepbin\System\Mvc\Model;


/**
 * 渲染时，通过Request的$requestType属性来决定是否返回模板内容
 * 
 * @author joson
 *
 */
class ResultModel extends Model
{
	
	/**
	 * 状态
	 * @var unknown
	 */
	public $status;
	
	
	/**
	 * 数据
	 * @var unknown
	 */
	public $data;
	
	
}