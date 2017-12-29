<?php
namespace Sepbin\System\Util;

/**
 * 实例集合的可通知严格模式接口
 * @author joson
 *
 */
interface IInstanceSetStrict
{
	
	/**
	 * 回滚方法
	 * 在实例集合的严格模式中，如果之后的方法发生错误，返回false
	 * 则已执行过的实例，将会被回调此方法
	 * @param string $method_name
	 */
	public function rollback( string $method_name );
	
}