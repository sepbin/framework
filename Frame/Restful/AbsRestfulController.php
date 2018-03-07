<?php
namespace Sepbin\System\Frame\Restful;

use Sepbin\System\Frame\Hook\IMvcRouteHook;
use Sepbin\System\Core\Request;
use Sepbin\System\Frame\Model;
use Sepbin\System\Frame\Mvc\AbsMvcController;

abstract class AbsRestfulController extends AbsMvcController implements IMvcRouteHook
{
    
    public function _init(\Sepbin\System\Util\FactoryConfig $config){
        
        getApp()->registerHook(IMvcRouteHook::class, $this);
        
    }
    
    
    public function actionBefore( string $action ) : string {
        
        if( $action != 'index' ) return $action;
        
        
        if( getApp()->getRequest()->getHttpMethod() == Request::REQUEST_HTTP_GET ){
            
            return 'read';
            
        }
        
        if( getApp()->getRequest()->getHttpMethod() == Request::REQUEST_HTTP_PUT ){
            
            return 'create';
            
        }
        
        
        if( getApp()->getRequest()->getHttpMethod() == Request::REQUEST_HTTP_DELETE ){
            
            return 'delete';
            
        }
        
        if( getApp()->getRequest()->getHttpMethod() == Request::REQUEST_HTTP_POST ){
            
            return 'update';
            
        }
        
        if( getApp()->getRequest()->getHttpMethod() == Request::REQUEST_HTTP_OPTIONS ){
            
            return 'options';
            
        }
        
        
        
    }
    
    public function optionsAction(){
        
        $allow = array();
        
        if( method_exists($this, 'indexAction') ){
            $allow[] = 'GET';
        }
        
        if( method_exists($this, 'createAction') ){
            $allow[] = 'PUT';
        }
        
        if( method_exists($this, 'updateAction') ){
            $allow[] = 'POST';
        }
        
        if( method_exists($this, 'deleteAction') ){
            $allow[] = 'DELETE';
        }
        
        getApp()->getResponse()->addHeader('Allow:'. implode(',', $allow));
        
        $model = new Model();
        $model->allow = $allow;
        
        return $model;
        
    }
	
	
}