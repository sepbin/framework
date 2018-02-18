<?php
namespace Sepbin\System\Cache\Exception;

use Sepbin\System\Core\SepException;

class CacheDriverError extends SepException
{
    
    protected $msg = '缓存驱动错误，没有找到类型，或类型不是ACache';
    
    protected $code = 1030;
    
}