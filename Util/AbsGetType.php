<?php
namespace Sepbin\System\Util;


use Sepbin\System\Core\Base;

abstract class AbsGetType extends Base
{
	
	
	/**
	 * 获取一个键值
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	abstract public function get( string $name, $default='' );
	
	
	/**
	 * 获取一个字符串类型的键值
	 * @param string $name
	 * @param string $default
	 * @return string
	 */
	public function getStr( string $name, string $default='' ):string{
		
		$result = $this->get($name,$default);
		
		return $result.'';
		
	}
	
	
	/**
	 * 获取一个字符串类型的键值，并限定长度
	 * @param string $name
	 * @param string $default
	 * @param int $limit
	 * @return string
	 */
	public function getStrLimit( string $name, string $default='', int $limit=0 ):string{
		
		$result = $this->getStr($name,$default);
		
		if($limit > 0){
			$result = StringUtil::substrFirstLength($result, $limit);
		} 
		
		return $result;
		
	}
	
	
	/**
	 * 获取一个布尔类型的键值
	 * @param string $name
	 * @param bool $default
	 * @return bool
	 */
	public function getBool( string $name, bool $default=false ):bool{
		
		$result = $this->get($name,$default);
		
		if( $result !== 'false' && !empty($result) ) return true;
		
		return false;
		
	}
	
	
	/**
	 * 获取一个整数类型的键值
	 * @param string $name
	 * @param int $default
	 * @return int
	 */
	public function getInt( string $name, int $default=0 ):int{
		
		$result = $this->get($name,$default);
		
		return intval($result);
	}
	
	
	/**
	 * 获取一个浮点类型的键值
	 * @param string $name
	 * @param float $default
	 * @return float
	 */
	public function getFloat( string $name, float $default=0 ):float{
		
		$result = $this->get($name,$default);
		
		return doubleval($result);
		
	}
	
	
	/**
	 * 获取一个固定小数位的浮点字符串的键值
	 * @param string $name
	 * @param string $default
	 * @param int $place
	 * @return string
	 */
	public function getDecimal( string $name, string $default='0', int $place=2 ):string{
		
		$result = $this->getFloat($name,$default);
		return number_format($result,$place);
		
	}
	
	
	
	/**
	 * 获取一个数组类型的键值，但数组中保存的数据类型未知
	 * @param string $name
	 * @param array $default
	 * @return array
	 */
	public function getArr( string $name, array $default=array() ):array{
		
		$result = $this->get($name,$default);
		if(!is_array($result)){
			return array( $result );
		}
		return $result;
		
	}
	
	
	/**
	 * 获取一个由字符组成的数组类型的键值
	 * @param string $name
	 * @param array $default
	 * @return string[]
	 */
	public function getArrStr( string $name, array $default=array() ):array{
		
		$result = $this->getArr( $name, $default );
		
		if(empty($result)) return $result;
		
		foreach ($result as $key=>$val){
			$result[$key] = $val.'';
		}
		
		return $result;
		
	}
	
	
	/**
	 * 获取一个由布尔组成的数组类型的键值
	 * @param string $name
	 * @param array $default
	 * @return bool[]
	 */
	public function getArrBool( string $name, array $default=array() ):array{
		
		$result = $this->getArr( $name, $default );
		
		if(empty($result)) return $result;
		
		foreach ($result as $key=>$val){
			
			if( $val !== 'false' && !empty($val) ) $result[$key] = true;
			else $result[$key] = false;
			
		}
		
		return $result;
		
	}
	
	
	/**
	 * 获取一个由整数组成的数组类型的键值
	 * @param string $name
	 * @param array $default
	 * @return int[]
	 */
	public function getArrInt( string $name, array $default=array() ):array{
		
		$result = $this->getArr( $name, $default );
		
		if(empty($result)) return $result;
		
		foreach ($result as $key=>$val){
			$result[$key] = intval($val);
		}
		
		return $result;
		
	}
	
	
	/**
	 * 获取一个由浮点组成的数组类型的键值
	 * @param string $name
	 * @param array $default
	 * @return float[]
	 */
	public function getArrFloat( string $name, array $default=array() ):array{
		
		$result = $this->getArr( $name, $default );
		
		if(empty($result)) return $result;
		
		foreach ($result as $key=>$val){
			$result[$key] = doubleval($val);
		}
		
		return $result;
		
	}
	
	public function getArrDecimal( string $name, array $default=array(), int $place=2 ):array{
		
		$result = $this->getArrFloat($name,$default);
		
		if(empty($result)) return $result;
		
		foreach ($result as $key=>$val){
			$result[$key] = number_format($val, $place);
		}
		
		return $result;
		
	}
	
}