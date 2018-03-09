<?php
namespace Sepbin\System\Util\Data;

class UniqueName
{
    
    
    /**
     * 基于时间创建唯一名字
     * @param string $date_format
     */
    static public function timeBased( $date_format = 'YmdHis' ){
        
        $date = date( $date_format );
        $date = Base62::encode($date);
        $tmp = explode(' ', microtime());
        $mic = Base62::encode(  intval( $tmp[0] * 1000000 ) );
        $rand = mt_rand(10000,99999);
        $rand = Base62::encode($rand);
        return $date.$mic.$rand;
        
    }
    
}