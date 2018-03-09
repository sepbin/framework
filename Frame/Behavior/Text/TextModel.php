<?php
namespace Sepbin\System\Frame\Behavior\Text;

use Sepbin\System\Frame\Model;

class TextModel extends Model
{
    
    public $text;
    
    static public function fromString( string $str ){
        
        $model = new TextModel();
        $model->text = $str;
        return $model;
        
    }
    
}