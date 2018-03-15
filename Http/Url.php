<?php
namespace Sepbin\System\Http;

class Url
{
    
    /**
     * 获取当前路由路径
     * @return string
     */
    static public function getPath() : string{
        
        return getApp()->currentPath;
        
    }
    
    
    static public function prePath( $path = '', $append='' ) : string{
        
        if($append == '') return $path;
        
        return $append.'/'.$path;
        
    }
    
    
    static public function getUrl( string $path = '', $query=[], bool $hold_query = false ) : string {
        
        $url = '/'.$path;
        if( getApp()->httpRewrite ){
            return HTTP_ROOT.$url;
        }
        
        if( $url == '/' ) $url = '';
        
        $url = HTTP_ROOT.'/index.php'.$url;
        
        if( is_string($query) ){
            if($query == '') $query = [];
            else{
                parse_str($query,$query);
                foreach ($query as $k=>$v){
                    if($v == '') $query[$k] = null;
                }
            }
            
        }
        
        if( $hold_query ){
            $currentQuery = $_SERVER['QUERY_STRING'];
            if(!empty($currentQuery)){
                parse_str($currentQuery, $queryArr);
                $query = array_merge( $queryArr, $query );
            }
        }
        
        if(!empty($query)){
            $url .= '?'.http_build_query($query);
        }
        
        return $url;
        
    }
    
    
}