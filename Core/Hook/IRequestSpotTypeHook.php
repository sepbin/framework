<?php
namespace Sepbin\System\Core\Hook;

interface IRequestSpotTypeHook
{
	
    /**
     * 请求类型过滤
     * 你可以实现此方法，以加入自己的判断逻辑
     * @param string $request_type
     * @param \Sepbin\System\Core\Request $request
     * @return string
     */
	public function spot( string $request_type, \Sepbin\System\Core\Request $request ):string;
	
}