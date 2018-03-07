<?php
namespace Sepbin\System\Route;

use Sepbin\System\Core\Exception\NotFoundException;
use Sepbin\System\Core\IRouteEnable;
use Sepbin\System\Core\Exception\RouteDelegateException;
use Sepbin\System\Util\Factory;
use Sepbin\System\Core\Request;

class BasicRoute implements IRoute
{
    
    private $rule = [];   
    
    public function addRoute($rule, $delegate, $params = array(), $restrict= array() ) {
        
        $this->rules [ $rule ] = array (
            'delegate' => $delegate,
            'params' => $params,
            'restrict' => $restrict
        );
        
    }
    
    
    /**
     * 执行路由
     */
    public function route( $host, $path ){
        
        $isFind = false;
        
        if(empty($this->rules)) return false;
        
        foreach ( $this->rules as $rule => $run ) {
        	
        	if (false !== ($result = $this->match ( $rule, $host, $path, $run['restrict'] ))) {
                
                if (is_callable ( $run ['delegate'] )) {
                    
                    $isFind = true;
                    $run ['delegate'] ( $run['params'] );
                    
                } elseif (! empty ( $run ['delegate'] )) {
                    
                    $isFind = true;
                    $delegate = Factory::getForString ( $run ['delegate'] );
                    
                    if (! $delegate instanceof IRouteEnable) {
                        throw (new RouteDelegateException ())->appendMsg ( $run ['delegate'] );
                    }
                    
                    if (! empty ( $run ['params'] )) {
                        $result = array_merge ( $result, $run ['params'] );
                    }
                    
                    $delegate->RouteMapper ( $result );
                }
                break;
            }
            
        }
        
        if (! $isFind && ! empty ( $this->rules )) {
            
            throw (new NotFoundException ())->appendMsg ( $path );
            
        }
    }
    
    
    /**
     * 匹配路由规则
     * @param unknown $rule
     * @param unknown $path
     * @return array|boolean|mixed[]
     */
    private function match($rule, $host, $path, $restrict) {
        
        
        $result = array ();
        
        if ($rule == '')
            return $result;
            
            $ruleHost = substr ( $rule, 0, strpos ( $rule, '://' ) );
            
            
            if( ($host == 'cli' && $ruleHost != 'cli') || ($ruleHost == 'cli' && $host != 'cli') ){
            	return false;
            }
            
            if( $ruleHost != 'host' && $host != 'host' && $ruleHost != $host ){
            	return false;
            }
            
            $rule = substr ( $rule, strpos ( $rule, '://' ) + 3 );
            
            if ($rule == '' && $path == '') return $result;
            elseif ($rule == '') return false;
            
            $ruleTmp = explode ( '/', $rule );
            $pathTmp = explode ( '/', $path );
            
            if (count ( $ruleTmp ) != count ( $pathTmp )) return false;
            
            for($i = 0; $i < count ( $ruleTmp ); $i ++) {
                
                if (strpos ( $ruleTmp [$i], '{' ) !== false && strrpos ( $ruleTmp [$i], '}' ) !== false) {
                    
                    $paramKey = substr ( $ruleTmp [$i], 1, strrpos ( $ruleTmp [$i], '}' ) - 1 );
                    
                    //检查限定规则
                    if( isset($restrict[$paramKey]) ){
                        $matches = [];
                        if( !preg_match('/^'.$restrict[$paramKey].'$/', $pathTmp[$i], $matches) ){
                            return false;
                        }
                        
                        $newVal = $matches[ count($matches) - 1 ];
                        getApp()->request->param->put ( $paramKey, $newVal );
                        $result[ $paramKey ] = $newVal;
                        
                    }else{
                        
                        getApp()->request->param->put ( $paramKey, $pathTmp [$i] );
                        $result [$paramKey] = $pathTmp [$i];
                        
                    }
                    
                    
                    
                } elseif ($ruleTmp [$i] != $pathTmp [$i]) {
                    
                    return false;
                    
                }
            }
            
            return $result;
    }
    
    
}