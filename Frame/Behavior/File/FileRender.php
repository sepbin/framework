<?php
namespace Sepbin\System\Frame\Behavior\File;

use Sepbin\System\Frame\AbsRender;
use Sepbin\System\Frame\Model;
use Sepbin\System\Core\Exception\NotFoundException;

class FileRender extends AbsRender
{
    
    
    public function get( Model $model ){
        
        
        if( !file_exists($model->filename) ){
            throw (new NotFoundException())->appendMsg( 'file:'.$model->filename );
        }
        
        
        $out = new FileResponse();
        $out->responseFilename = $model->name;
        $out->responseFromFile = $model->filename;
        
        getApp()->response->out = $out;
        
        if( getApp()->xSendfile == false ){
            $http = getHttp();
            $filesize = filesize( $model->filename );
            $http->addHeader( 'Content-Length: '.$filesize );
        }
        
        
        return '';
        
    }
    
    
}