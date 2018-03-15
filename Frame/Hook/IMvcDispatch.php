<?php
namespace Sepbin\System\Frame\Hook;

interface IMvcDispatch
{
    
    /**
     * 派遣之前判断是否允许派遣
     * @param string $module
     * @param string $controller
     * @param string $action
     * @return bool
     */
    public function dispatchBefore( string $module, string $controller, string $action ) : bool;
    
    
    /**
     * 派遣之后判断是否允许输出
     * @param unknown $result
     * @return bool
     */
    public function dispatchAfter( $result ) : bool;
    
}