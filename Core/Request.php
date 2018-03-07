<?php
namespace Sepbin\System\Core;
use Sepbin\System\Util\InstanceSet;
use Sepbin\System\Core\RequestParam;
use Sepbin\System\Core\Hook\IRequestSpotTypeHook;

class Request extends Base
{
	
	/**
	 * 浏览请求
	 * 浏览器的普通请求可定义为broswer请求
	 * 在这个模式下，响应用户时，系统更偏向于返回带有html等的有显示界面的数据
	 * @var string
	 */
	const REQUEST_TYPE_BROSWER = 'broswer';
	
	
	/**
	 * 提交请求
	 * js的ajax,app的httprequest等可定义为post请求
	 * 在这个模式下，响应用户时，系统更偏向于返回json、xml等格式的数据
	 * @var string
	 */
	const REQUEST_TYPE_POST = 'post';
	
	
	/**
	 * 控制台请求
	 * 在控制台使用php命令执行的可定义为console请求
	 * 在这个模式下，响应用户时，系统更偏向于返回文本格式的数据
	 * @var string
	 */
	const REQUEST_TYPE_CONSOLE = 'console';
	
	
	
	
	
	
	//对应http的请求类型
	const REQUEST_HTTP_GET = 'get';
	const REQUEST_HTTP_POST = 'post';
	const REQUEST_HTTP_PUT  = 'put';
	const REQUEST_HTTP_DELETE = 'delete';
	const REQUEST_HTTP_OPTIONS = 'options';
	
	
	
	
	
	/**
	 * 请求的方式
	 * @var string
	 */
	private $requestType;
	
	
	/**
	 * http请求方式
	 * @var string
	 */
	private $requestHttpMethod;
	
	
	/**
	 * 请求语言
	 * @var string
	 */
	public $requestLang;
	
	/**
	 * 请求参数
	 * @var RequestParam
	 */
	public $param;
	
	
	function __construct(){
		
		getApp()->registerHook(IRequestSpotTypeHook::class, RequestSpotTypeDefault::class);
		
		$this->spotHttpMethod();
		
		$this->requestType = getApp()->hook(IRequestSpotTypeHook::class, 'spot', InstanceSet::CALL_TUNNEL, self::REQUEST_TYPE_BROSWER, $this);
		
		if( $this->requestType != self::REQUEST_TYPE_CONSOLE ){
			
			if ($this->requestHttpMethod == self::REQUEST_HTTP_POST){
				$this->param = new RequestParam( $_GET, $_POST, $_FILES );
			}else{
				$this->param = new RequestParam( $_GET );
			}
			
		}else{
			
			$this->param = new RequestParam( $this->getConsoleParams() );
			
		}
		
		$this->requestLang = getApp()->defaultLang;
		
	}
	
	
	private function getConsoleParams(){
		
		$args = $_SERVER['argv'];
		
		unset($args[0]);
		
		$params = array();
		
		
		
		$isKey = function($arg){
			if( substr($arg, 0, 1) == '-' || substr($arg, 0, 2) == '--' ){
				
				if( $arg == '-command' || $arg == '--command' ){
					return false;
				}
				
				return true;
			}
			return false;
		};
		
		if( !empty($args[1]) && !empty($args[2]) && !$isKey($args[1]) && !$isKey($args[2]) ){
			$params['command'] = $args[2];
		}
		
		for( $i=1; $i<count($args)+1; $i++ ){
			if( $isKey( $args[$i] ) ){
				$result = preg_match('/-(\w+)=(.+)/', $args[$i],$matches);
				if($result){
					$params[ $matches[1] ] = $matches[2];
					continue;
				}
				
				$args[$i] = str_replace('-', '', $args[$i]);
				
				if( !isset($args[$i+1]) ){
					$params[ $args[$i] ] = true;
					continue;
				}
				
				if( $isKey( $args[$i+1] ) ){
					$params[ $args[$i] ] = true;
				}else{
					$params[ $args[$i] ] = $args[$i+1];
					$i++;
				}
			}
		}
		
		
		return $params;
		
	}
	
	
	/**
	 * 获取全部输入数据
	 * @return string
	 */
	public function getInput():string{
		
		return file_get_contents("php://input");
		
	}
	
	
	/**
	 * 把input的数据，当作参数格式传入统一参数
	 */
	public function putInputParam(){
		
		$input = $this->getInput();
		if(!empty($input)){
			parse_str($input, $arr);
			$this->param->appendParam($arr);
		}
		
	}
	
	/**
	 * 识别当前请求的HTTP类型
	 */
	private function spotHttpMethod(){
		
		$method = isset($_SERVER['REQUEST_METHOD'])? $_SERVER['REQUEST_METHOD'] : 'GET';
		
		switch ( $method ){
			case 'GET':
				$this->requestHttpMethod = self::REQUEST_HTTP_GET;
				break;
			case 'POST':
				$this->requestHttpMethod = self::REQUEST_HTTP_POST;
				break;
			case 'PUT':
				$this->requestHttpMethod = self::REQUEST_HTTP_PUT;
				break;
			case 'DELETE':
				$this->requestHttpMethod = self::REQUEST_HTTP_DELETE;
				break;
			default:
				$this->requestHttpMethod = self::REQUEST_HTTP_GET;
				break;
		}
		
	}
	
	
	/**
	 * 获取当前http请求的方式
	 * @return string
	 */
	public function getHttpMethod() : string{
		
		return $this->requestHttpMethod;
		
	}
	
	/**
	 * 设置请求方式
	 * @param string $requestType
	 */
	public function setRequestType( string $requestType ){
		
		$this->requestType = $requestType;
		
	}
	
	/**
	 * 获取请求方式
	 * @return string
	 */
	public function getRequestType():string{
		
		return $this->requestType;
		
	}
	
	
	/**
	 * 获取参数类
	 * @return RequestParam
	 */
	public function getParams() : RequestParam{
		
		return $this->param;
		
	}
	
}