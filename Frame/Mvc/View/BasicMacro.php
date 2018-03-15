<?php
namespace Sepbin\System\Frame\Mvc\View;

use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Http\Url;

class BasicMacro extends AbsMacro
{
    
    
    public function __D_ROOT_URL( $file='' ){
        
        return HTTP_ROOT.'/'.$file;
        
    }
    
    
    public function __D_URL( $path='' ){
        
        return Url::getUrl( $path );
        
    }
    
    
    
    public function __D_VIEWPATH( $file='' ){
        
        return $this->manager->stylePath.'/'.$file;
        
    }
    
    
    public function __O_URL( $path = '', string $query='', bool $hold_query=false ){
         
        return SyntaxUtil::phpTag( ' \Sepbin\System\Http\Url::getUrl( 
                '.SyntaxUtil::macroVar($path).', 
                '.SyntaxUtil::macroVar($query).', 
                '.SyntaxUtil::macroVar($hold_query).' ) ' );
        
        
    }
    
    
    public function __O_ACTION( $path, ...$params ){
        
        $tmp = explode('/', $path);
        
        $module = isset($tmp[0])?  ClassName::underlineToCamel($tmp[0],true) : 'Index';
        $controller = isset($tmp[1])? ClassName::underlineToCamel($tmp[1],true) : 'Layouts';
        $action = isset($tmp[2])? ClassName::underlineToCamel($tmp[2]): 'index';
        
        
        $str = '$this->manage->includeController(';
        
        $str.= SyntaxUtil::macroVar($module).',';
        $str.= SyntaxUtil::macroVar($controller).',';
        $str.= SyntaxUtil::macroVar($action);
        
        if(!empty($params)){
            $params = array_map(function($val){
                return SyntaxUtil::macroVar($val);
            }, $params);
            
            $params = implode(',', $params);
            $params = ','.$params;
                
            $str.=$params;
        }
        
        $str.= ')';
        
        return SyntaxUtil::phpTag( $str );
        
    }
    
    
    
    public function __O_CONTENT( $key ){
        
        return SyntaxUtil::phpTag(' echo $this->manage->getExtendContent(\''.$key.'\') ');
        
    }
    
    
    
    
    public function __O_EXTENDS_END( $key='' ){
        
        if( $key == '' ){
            return SyntaxUtil::phpTag(' if(!$this->manage->ignoreParent) ob_end_clean() ');
        }
        
        return
        SyntaxUtil::phpTag('
				if(!$this->manage->ignoreParent){
					ob_end_clean();
					echo $this->manage->getExtendContent(\''.$key.'\');
				}
		');
        
    }
    
    
    
    
    public function __O_EXTENDS_SET( $key, $value ){
        
        return SyntaxUtil::phpTag('
            if(!$this->manage->ignoreParent){
			     $this->manage->extendContent[\''.$key.'\'] = \''.$value.'\';
            }
            
		');
        
    }
    
    
    
    
    public function __O_EXTENDS_START( $key ){
        
        
        return SyntaxUtil::phpTag('
            
			if( !$this->manage->ignoreParent ){
				ob_start(function($content){
					$this->manage->putExtendContent('.SyntaxUtil::macroVar($key).',$content); return "error:'.$key.'";
				});
			}
            
		');
        
    }
    
    
    
    public function __O_EXTENDS( $path, ...$params ){
        
        
        $tmp = explode('/', $path);
        
        $module = isset($tmp[0])?  ClassName::underlineToCamel($tmp[0],true) : 'Index';
        $controller = isset($tmp[1])? ClassName::underlineToCamel($tmp[1],true) : 'Layouts';
        $action = isset($tmp[2])? ClassName::underlineToCamel($tmp[2]): 'index';
        
        
        return SyntaxUtil::phpTag('
           if(!$this->manage->ignoreParent){
			$this->manage->isParent = true;
			$this->manage->parentModule = '.SyntaxUtil::macroVar($module).';
			$this->manage->parentController = '.SyntaxUtil::macroVar($controller).';
			$this->manage->parentAction = '.SyntaxUtil::macroVar($action).';
			$this->manage->parentParams = '.SyntaxUtil::macroVar($params).';
			$this->manage->parentFilename = \''. $this->manager->getFilename( $this->manager->getControllerTplFile( $module, $controller, $action ) ) .'\';
           }
		');
        
        
    }   
    
    
    
    
    PUBLIC FUNCTION __O_INCLUDE( $filename ){
        
        if(empty($filename)) return '';
        
        $fullname = $this->manager->styleDir.'/'.$filename;
        $fullname = trim($fullname);
        
        return "\n<!--include $filename-->\n".SyntaxUtil::phpTag('$this->manage->includeContent( $this, '.$this->getVarOrStr($fullname).' )')."\n\n<!--include end-->\n";
        
    }
    
}