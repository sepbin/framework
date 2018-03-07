<?php
namespace Sepbin\System\Frame\Redirect;

use Sepbin\System\Frame\Model;

class RedirectModel extends Model
{
	
	/**
	 * 用url字符串构造一个RedirectModel实例
	 * @param string $url 如果服务器没有开启rewrite，但传入值也不要带有index.php
	 * @param int $status
	 * @return RedirectModel
	 */
	static public function get( string $url, int $status = self::MOVE_TEMPORARILY ) : RedirectModel{
		
		$url = str_replace('/index.php', '', $url);
		if( !strpos('://', $url) ){
			$url = getApp()->defaultScheme.'://'.$url;
		}
		
		$urlInfo = parse_url($url);
		
		$model = new RedirectModel();
		$model->httpStatus = $status;
		$model->redirectUrl = isset($urlInfo['path']) ? ltrim($urlInfo['path'],'/') : '';
		
		$model->host = $urlInfo['host'];
		$model->scheme = $urlInfo['scheme'];
		$model->port = isset($urlInfo['port'])?$urlInfo['port']:0;
		
		return $model;
		
	}
	
	/**
	 * 永久性转移
	 * 被请求的资源已永久移动到新位置，并且将来任何对此资源的引用都应该使用本响应返回
	 * 的若干个 URI 之一。如果可能，拥有链接编辑功能的客户端应当自动把请求的地址修改
	 * 为从服务器反馈回来的地址。除非额外指定，否则这个响应也是可缓存的。
	 * @var integer
	 */
	const MOVED_PERMANENTLY = 301;
	
	/**
	 * 临时转移
	 * 请求的资源临时从不同的 URI响应请求。由于这样的重定向是临时的，客户端应当继续向
	 * 原有地址发送以后的请求。只有在Cache-Control或Expires中进行了指定的情况下，
	 * 这个响应才是可缓存的
	 * @var integer
	 */
	const MOVE_TEMPORARILY = 302;
	
	/**
	 * 参见其他地址
	 * 对应当前请求的响应可以在另一个 URI 上被找到，而且客户端应当采用 GET 的方式访
	 * 问那个资源。这个方法的存在主要是为了允许由脚本激活的POST请求输出重定向到一个新
	 * 的资源。这个新的 URI 不是原始资源的替代引用。同时，303响应禁止被缓存。当然，
	 * 第二个请求（重定向）可能被缓存。
	 */
	const SEE_OTHER  = 303;
	
	
	/**
	 * 响应状态码
	 * @var int
	 */
	public $httpStatus = self::MOVE_TEMPORARILY;
	
	
	/**
	 * 跳转URL
	 * 不要带根路径
	 * 如http://localhost/sepbin3/public/cont/doc，只需要传入 cont/doc
	 * @var string
	 */
	public $redirectUrl = '';
	
	/**
	 * 域名
	 * @var string
	 */
	public $host = '';
	
	/**
	 * 协议
	 * @var string
	 */
	public $scheme = '';
	
	
	/**
	 * 端口号
	 * @var integer
	 */
	public $port = 0;
	
	
	public function getUrl(){
		
		$url = $this->scheme.'://';
		$url.= $this->host == 'host'?$_SERVER['HTTP_HOST']:$this->host ;
		
		if( $this->port > 0 && ($this->scheme=='http' && $this->port != 80) && ($this->scheme=='https' && $this->port != 443) ){
			$url.=':'.$this->port;
		}
		
		$url.= HTTP_ROOT;
		
		if( !getApp()->httpRewrite ){
			$url.= '/index.php';
		}
		
		if( !empty($this->redirectUrl) ){
			$url.= '/'.$this->redirectUrl;
		}
		
		return $url;
		
	}
	
}