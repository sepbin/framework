<?php
namespace Sepbin\System\Http;

use Sepbin\System\Core\Base;

class Request extends Base
{
	
	const MEDIA_TYPE_PC = 'pc';
	
	const MEDIA_TYPE_MOBILE = 'mobile';
	
	const MEDIA_TYPE_PAD = 'pad';
	
	const REQUEST_TYPE_GET = 'get';
	
	const REQUEST_TYPE_AJAX = 'ajax';
	
	/**
	 * 请求的设备类型
	 * @var string
	 */
	private $mediaType;
	
	/**
	 * 请求的方式
	 * @var string
	 */
	private $requestType;
	
	
	public $param;
	
	
	function __construct(){
		
		
		
	}
	
}