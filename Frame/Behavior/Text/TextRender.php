<?php
namespace Sepbin\System\Frame\Behavior\Text;

use Sepbin\System\Frame\AbsRender;
use Sepbin\System\Frame\Model;

class TextRender extends AbsRender
{
    
    public function get( Model $model ){
        
        getApp()->response->out = new TextResponse();
        return $model->text;
        
    }
    
}