<?php
namespace Sepbin\System\Core;

use Sepbin\System\Util\Data\ArrayXML;

class ResponseOutDefault implements IResponseHijack
{
    
    
    public function browser( array $buffer ){
        
        $http = getHttp();
        $http->setContentType('html');
        $http->sendHeader();
        foreach ($buffer as $item){
            if(is_string($item)){
                echo $item." \n";
            }else{
                echo '<pre>';
                var_export($item);
                echo '</pre>';
            }
        }
        
    }
    
    public function post( array $buffer ){
        
        $http = getHttp();
        $http->setContentType( getApp()->defaultDataFormat );
        
        $otherStr = null;
        $data = array();
        
        foreach ($buffer as $item){
            if(empty($item)) continue;
            if( is_array($item) ){
                $data = array_merge( $data, $item );
            }else{
                $otherStr .= trim($item).' ';
            }
        }
        
        if( $otherStr !== null ){
            $data['__other_text'] = $otherStr ;
        }
        
        $http->sendHeader();
        
        if($http->contentType == 'text/xml'){
            echo ArrayXML::arrayToXmlString($data);
        }else{
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }
        
        
    }
    
    
    public function console( array $buffer ){
        
        
        foreach ($buffer as $item){
            if( is_string($item) ){
                echo strip_tags( $item ) . PHP_EOL;
            }else{
                var_export( $item );
            }
        }
        
        
    }
    
}