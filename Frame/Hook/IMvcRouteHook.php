<?php
namespace Sepbin\System\Frame\Hook;

interface IMvcRouteHook
{
    
    /**
     * action执行之前
     * 您有机会在这个接口改变正要执行的action方法
     * @param string $action
     * @return string
     */
    public function actionBefore( string $action ) : string ;
    
}