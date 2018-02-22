<?php
namespace Sepbin\System\Frame\Hook;

interface IMvcRouteHook
{
    
    public function actionBefore( string $action ) : string ;
    
}