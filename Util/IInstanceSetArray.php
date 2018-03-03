<?php
namespace Sepbin\System\Util;

/**
 * 实例集合的可通知严格模式接口
 * @author joson
 *
 */
interface IInstanceSetArray
{
	
	/**
	 * 回滚方法
	 * 在实例集合的数组模式中，如果方法返回false，
	 * 则回调此方法
	 * @param string $method_name
	 */
	public function rollback( string $method_name ):void;
	
}