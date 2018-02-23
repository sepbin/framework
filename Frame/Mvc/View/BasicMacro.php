<?php
namespace Sepbin\System\Frame\Mvc\View;

use Sepbin\System\Util\Data\ClassName;

class BasicMacro extends AbsMacro
{
    
    
    public function __D_ROOT_URL( $file='' ){
        
        return HTTP_ROOT.'/'.$file;
        
    }
    
    
    public function __D_URL( $url='' ){
        
        $url = '/'.$url;
        
        if( getApp()->httpRewrite ){
            return HTTP_ROOT.$url;
        }
        
        if( $url == '/' ) $url = '';
        
        return HTTP_ROOT.'/index.php'.$url;
        
    }
    
    
    public function __D_VIEWPATH( $file='' ){
        
        return $this->manager->stylePath.'/'.$file;
        
    }
    
    public function __O_ACTION( $module, $controller, $action, ...$params ){
        
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
            
			$this->manage->extendContent[\''.$key.'\'] = \''.$value.'\';
            
		');
        
    }
    
    public function __O_EXTENDS_START( $key ){
        
        
        return SyntaxUtil::phpTag('
            
			if( !$this->manage->ignoreParent ){
				ob_start(function($content){
					$this->manage->putExtendContent(\''.$key.'\',$content); return "error:'.$key.'";
				});
			}
            
		');
        
    }
    
    public function __O_EXTENDS( $path, ...$params ){
        
        
        $tmp = explode('/', $path);
        
        $module = isset($tmp[0])?  ClassName::underlineToCamel($tmp[0],true) : 'Index';
        $controller = isset($tmp[1])? ClassName::underlineToCamel($tmp[1],true) : 'Layouts';
        $action = isset($tmp[2])? ClassName::underlineToCamel($tmp[2]): 'index';
        
        $params = array_map( function($val){
            
            return SyntaxUtil::macroVar($val);
            
        } , $params);
        
        
        return SyntaxUtil::phpTag('
                
			$this->manage->isParent = true;
			$this->manage->parentModule = \''.$module.'\';
			$this->manage->parentController = \''.$controller.'\';
			$this->manage->parentAction = \''.$action.'\';
			$this->manage->parentParams = [ '.implode(',', $params).' ];
			$this->manage->parentFilename = \''. $this->manager->getFilename($module, $controller, $action) .'\';
                
		');
        
    }   
    
    PUBLIC FUNCTION __O_INCLUDE( $filename ){
        
        if(empty($filename)) return '';
        
        $fullname = $this->manager->styleDir.'/'.$filename;
        $fullname = trim($fullname);
        
        return "\n<!--include $filename-->\n".SyntaxUtil::phpTag('$this->manage->includeContent( $this, '.$this->getVarOrStr($fullname).' )')."\n\n<!--include end-->\n";
        
    }
    
}