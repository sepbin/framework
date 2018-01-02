<?php
namespace Sepbin\System\Util;

/**
 * 数组工具
 * @author joson
 *
 */
class ArrayUtil
{
	
    /**
     * 把数组对象转化成字符串表达式，可以是多维数组
     * @static
     * @access public
     * @example $arr toExpString($arr) return string [1,2]
     * @param array $arr 要转化的数组
     * @return string
     */
    static public function toExpString( array $arr ) : string{
        
        $appContent = '[';
        foreach ($arr as $key=>$value){
            if ( $key != '' || $key===0 ){
                $appContent.= '\''.$key.'\'=>';
                if (is_string($value)){
                    $appContent.= '\''.$value.'\',';
                }elseif (is_array($value)){
                    $appContent.= self::toExpString($value).',';
                }elseif (is_numeric($value)) {
                    $appContent.= $value.',';
                }elseif (is_bool($value)){
                    if ($value){
                        $appContent.='true'.',';
                    }else{
                        $appContent.='false'.',';
                    }
                }
            }
        }
        $appContent = rtrim($appContent,',');
        $appContent.= ']';
        return $appContent;
        
    }
    
    
    
    /**
     * 把二维数组中的相同键的值组成新的数组
     * @static
     * @access public
     * @example [ ['a' => 1 , 'b'=>2], ['a' => 3] ] getArrayCol('a') return [ 1, 3 ]
     * @param array $array 原数组
     * @param string $key 要转化的键名
     * @param bool $unique 返回结果是否去掉重复
     * @return array|unknown[]
     */
    public static function getArrayCol(array $array, string $key, bool $unique=false) : array{
        
        $col = array();
        if(!empty($array)){
            foreach ($array as $item){
                $col[] = $item[$key];
            }
            
            if($unique)
                $col = array_unique($col);
                
        }
        return $col;
        
    }
    
    
    /**
     * 把一个二维数组按照数组中的某个键值组成新的二维数组
     * @static
     * @access public
     * @example array( array('k'=>1,'v'=>2), array('k'=>1,'v'=>3) ) 转成
     * array( '1' => array(2,3) )
     * @param array $array 要转化的数组
     * @param string $group_key 作为键的键值
     * @param string $value_key 作为值的键值
     * @return array|unknown[]
     */
    public static function getArrayKeyValArr(array $array, string $group_key, string $value_key) : array{
        
        if(empty($array)) return array();
        
        $data = array();
        foreach($array as $item){
            $data[$item[$group_key]][] = $item[$value_key];
        }
        return $data;
        
    }
    
    
    /**
     * 获取一个新数组，原数组为二维数组，把原数组的某个键值作为新数组的键，再把原数组的某个键值作为新数组的值
     * @static
     * @access public
     * @example [ ['a'=>1,'b'=>2], ['a'=>3,'b'=>4] ]  
     * getArrayKeyVal($arr,'a','b')
     * 返回 [ 1=>2, 3=>4 ]
     * @param array $array 二维原数组
     * @param string $group_key 作为新数组键的键值
     * @param string $value_key 作为新数组值的键值
     * @return array|unknown[]
     */
    public static function getArrayKeyVal(array $array, string $group_key, string $value_key):array{
    	
    	if(empty($array)) return array();
    	
    	$data = array();
    	foreach($array as $item){
    		$data[$item[$group_key]] = $item[$value_key];
    	}
    	
    	return $data;
    	
    }
    
    
    /**
     * 把一个二维数组以某个键值为依据分组
     * @static
     * @access public
     * @example [ [ 'a' => 1 ,2,3 ], ['a'=>1,4,5] ]
     * getArrayKeyArr( $arr, 'a' )
     * 返回 [ 1 => [ [ 'a' => 1 ,2,3 ], ['a'=>1,4,5] ] ]
     * @param array $array 原数组
     * @param string $group_key 键名
     * @return array|unknown[]
     */
    public static function getArrayKeyArr(array $array, string $group_key):array{
    	if(empty($array)) return array();
    		
    	$data = array();
    	foreach ($array as $item){
    		$data[ $item[$group_key] ][] = $item;
    	}
    	return $data;
    }
    
    
    /**
     * 把一个二位数组的某个键，作为其一级键
     * @static
     * @access public
     * @example [ ["a"=>1],["a"=>2] ]
     * getArrayColKey($arr,'a')
     * 返回 [ 1=>["a"=>1], 2=>["a"=>2] ]
     * @param array $array 原数组
     * @param string $group_key 键名
     * @return array|unknown[]
     */
    public static function getArrayColKey(array $array, string $group_key):array{
    	
    	if(empty($array)) return array();
    		
    	$data = array();
    	foreach ($array as $item){
    		$data[ $item[$group_key] ] = $item;
    	}
    	
    	return $data;
    	
    }
    
    
    /**
     * 在一个二维数组中查找某个值是否存在
     * @static
     * @access public
     * @example [ ["a"=>1], ["a"=>2] ]
     * inArrayCol(1,$arr,'a')
     * 返回 true
     * @param string|int $need 需要查找的值
     * @param array $array
     * @param string|int $key 二维数组的键
     * @return boolean
     */
    public static function inArrayCol($need, array $array, $key):bool{
    	
    	foreach ($array as $item){
    		if($item[$key] == $need)
    			return true;
    	}
    	return false;
    	
    }
    
}