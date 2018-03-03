<?php
namespace Sepbin\System\Core;

/**
 * 异常基类
 * @author joson
 *
 */
class SepException extends \Exception
{
    
    protected $msg = 'famework error';
    
    protected $code = 1000;
    
    function __construct(string $msg=null, int $code=null, $previous=null){
        
    	if($code == null) $code = $this->code;
        if($msg == null) $msg = $this->msg;
        parent::__construct($msg,$code,$previous);
        
    }
    
    public function appendMsg( string $msg ) : SepException{
    	
    	$this->message .= ' ( '.$msg.' )';
    	
    	return $this;
    	
    }
    
    public function __toString() : string{
        
        $str = '';
        $str .= "[$this->code]:$this->message";
        return $str;
        
    }
    
}