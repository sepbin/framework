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
    
    public function addRoute($rule, $delegate, $params = array()) {
        
        $this->rules [ $rule ] = array (
            'delegate' => $delegate,
            'params' => $params
        );
        
    }
    
    
    /**
     * 执行路由
     */
    public function route(){
        
        if (getApp()->request->getRequestType () != Request::REQUEST_TYPE_CONSOLE) {
            if ($_SERVER ['PHP_SELF']) {
                $path = $_SERVER ['PHP_SELF'];
            } else {
                $path = $_SERVER ['REQUEST_URI'];
                if (strpos ( $path, '?' ) !== false) {
                    $path = substr ( $path, 0, strpos ( $path, '?' ) );
                }
            }
            
            $path = substr ( $path, strlen ( HTTP_ROOT ) );
            
            $path = str_replace ( '/index.php', '', $path );
            $path = ltrim ( $path, '/' );
            
            if($path == ''){
                $path = getApp()->defaultPath;
            }
        } else {
            
            if ( isset($_SERVER['argv'][1]) &&  substr ( $_SERVER ['argv'] [1], 0, 1 ) == '-' ) {
                $path = '';
            } else {
                $path = isset ( $_SERVER ['argv'] [1] ) ? $_SERVER ['argv'] [1] : '';
            }
            
        }
        
        $isFind = false;
        
        foreach ( $this->rules as $rule => $run ) {
            
            if (false !== ($result = $this->match ( $rule, $path ))) {
                
                if (is_callable ( $run ['delegate'] )) {
                    
                    $isFind = true;
                    $run ['delegate'] ();
                    
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
    private function match($rule, $path) {
        
        $result = array ();
        
        if ($rule == '')
            return $result;
            
            if (getApp()->request->getRequestType () != Request::REQUEST_TYPE_CONSOLE) {
                if (substr ( $rule, 0, 7 ) == 'host://') {
                    $host = $_SERVER ['HTTP_HOST'];
                } else {
                    $host = substr ( $rule, 0, strpos ( $rule, '://' ) );
                }
                if ($host != $_SERVER ['HTTP_HOST']) return false;
            } else {
                if (substr ( $rule, 0, 6 ) != 'cli://') return false;
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
                    getApp()->request->param->put ( $paramKey, $pathTmp [$i] );
                    $result [$paramKey] = $pathTmp [$i];
                } elseif ($ruleTmp [$i] != $pathTmp [$i]) {
                    return false;
                }
            }
            
            return $result;
    }
    
    
}