<?php
namespace Sepbin\System\Frame\Behavior\File;

use Sepbin\System\Core\IResponseHijack;
use Sepbin\System\Util\Data\ArrayXML;
use Sepbin\System\Util\FileUtil;


class FileResponse implements IResponseHijack
{
    
    public $responseFilename = '';
    
    public $responseFromFile = '';
    
    
    public function browser( array $buffer ){
        
        $http = getHttp();
        $show_name = urlencode($this->responseFilename);
        $show_name = str_replace("+", "%20", $show_name);
        $ext = FileUtil::getExtensionName($show_name);
        $http->setContentType( $ext );
        $http->addHeader('Pragma: public');
        $http->addHeader('Content-Disposition: attachment; filename="' . $show_name . '"');
        $http->addHeader('Content-Encoding: none');
        $http->addHeader("Content-Transfer-Encoding: binary" );
        
        if( getApp()->xSendfile == false ){
        	readfile($this->responseFromFile);
        }else{
        	$fromFile = '/'.ltrim($this->responseFromFile, DOCUMENT_ROOT);
        	$http->addHeader('X-Sendfile: '.$this->responseFromFile);
        	$http->addHeader('X-Accel-Redirect: '.$fromFile);
        }
        
    }
    
    public function post( array $buffer ){
        
        $http = getHttp();
        $http->setContentType( getApp()->defaultDataFormat );
        
        $otherStr = null;
        $data = array();
        
        foreach ($this->buffer as $item){
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
        
        $data['__out_file'] = $this->responseFilename;
        
        $http->sendHeader();
        
        if($this->contentType == 'text/xml'){
            echo ArrayXML::arrayToXmlString($data);
        }else{
            echo json_encode($data,JSON_UNESCAPED_UNICODE);
        }
        
    }
    
    public function console( array $buffer ){
        
        echo PHP_EOL;
        echo 'file from '. $this->responseFromFile.' out '.$this->responseFilename;
        echo PHP_EOL;
        return '';
        	
    }
    
}