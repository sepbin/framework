<?php
namespace Sepbin\System\Http;

class SessionID
{
    
    static public function getID(){
        
        return session_create_id('');
        
    }
    
}