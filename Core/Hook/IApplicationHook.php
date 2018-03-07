<?php
namespace Sepbin\System\Core\Hook;

interface IApplicationHook
{
	
	/**
	 * 应用开始时
	 * @param \Sepbin\System\Core\Application $app
	 */
	public function applicationStart( \Sepbin\System\Core\Application $app );
	
	
	/**
	 * 应用结束时
	 * @param \Sepbin\System\Core\Application $app
	 */
	public function applicationEnd( \Sepbin\System\Core\Application $app );
	
	
	/**
	 * 应用抛出异常时
	 * PHP底层抛出的致命错误同样会调用此方法
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	public function applicationException( int $errno, string $errstr, string $errfile, int $errline );
	
	
	/**
	 * 应用抛出警告时
	 * 除导致致命的异常信息都会调用此方法
	 * @param int $errno
	 * @param string $errstr
	 * @param string $errfile
	 * @param int $errline
	 */
	public function applicationWarning( int $errno, string $errstr, string $errfile, int $errline );
	
}