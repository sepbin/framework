<?php
namespace Sepbin\System\Further\CSRFToken;

use Sepbin\System\Frame\Hook\IMvcModelHook;

class CSRFTokenModelHook implements IMvcModelHook
{
    
    public function modelRenderBefore( \Sepbin\System\Frame\Model $model ) {
        
        $s = getSession();
        $model->CSRF_TOKEN = $s->get(EnableCSRFToken::$tokenName);
        
    }
    
    
    public function modelCreate( \Sepbin\System\Frame\Model $model) {
        
    }
    
}