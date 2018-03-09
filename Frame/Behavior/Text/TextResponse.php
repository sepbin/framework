<?php
namespace Sepbin\System\Frame\Behavior\Text;

use Sepbin\System\Core\ResponseOutDefault;


class TextResponse extends ResponseOutDefault
{
    
    public function browser( array $buffer ){
        $this->write($buffer);
    }
    
    public function post( array $buffer ){
        $this->write($buffer);
    }
    
    private function write( array $buffer){
        
        $http = getHttp();
        $http->setContentType('txt');
        
        if( count($buffer) < 2 ){
            echo $buffer[0];
        }else{
            foreach ($buffer as $item){
                echo $item;
                echo PHP_EOL;
            }
        }
    }
    
    
}