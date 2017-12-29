<?php
namespace Sepbin\System\Util;

use Sepbin\System\Core\Base;
use Sepbin\Util\Exception\InstanceSetTypeException;

/**
 * 实例集合
 * @author joson
 *
 */
class InstanceSet extends Base
{
	
	/**
	 * 隧道模式
	 * 使用隧道模式，返回值以如下方式操作：
	 * 将第一个方法的返回值，作为第二个方法的第一个参数，以此类推。
	 * 第一个参数就像经过一个隧道，或者过滤器一样，被多种方法所修改
	 * 后，得到最终结果
	 * @var integer
	 */
	const CALL_TUNNEL = 1;
	
	
	/**
	 * 集合模式
	 * 返回值将以数组的形式返回
	 * @var integer
	 */
	const CALL_ARRAY = 2;
	
	
	/**
	 * 严格模式
	 * 只要有1个返回false，则终止执行并返回false.
	 * 如果实例实现了\Sepbin\Core\IInstanceSetStrict接口，则还会调用已调用实例的rollback方法
	 * @var integer
	 */
	const CALL_BOOL_STRICT = 3;
	
	
	/**
	 * 当前执行模式
	 * @var integer
	 */
	private $callMode = 2;
	
	
	/**
	 * 实例保存集合
	 * @var array
	 */
	private $collection = array();
	
	
	/**
	 * 错误
	 * @var string
	 */
	private $error = '';
	
	
	/**
	 * 执行的类型
	 * @var string
	 */
	private $type = '';
	
	
	function __construct( string $type, int $call_model ){
		
		$this->callMode = $call_model;
		$this->type = $type;
		
	}
	
	
	/**
	 * 增加实例
	 * @param object $instance
	 */
	public function add( $instance){
		
		if( !$instance instanceof $this->type ){
			throw new InstanceSetTypeException();
		}
		
		$this->collection[] = $instance;
		
	}
	
	
	/**
	 * 设置模式
	 * @param const $mode
	 * @return \Sepbin\Core\InstanceSet
	 */
	public function setMode($mode){
		
		$this->callMode = $mode;
		
		return $this;
	}
	
	/**
	 * 得到索引所指向的一个实例
	 * @param int $index
	 * @return multitype:
	 */
	public function getIndex( int $index){
		
		if ( isset($this->collection[$index]) ) return $this->collection[$index];
		
	}
	
	
	/**
	 * 得到集合的长度
	 * @return int
	 */
	public function getLength(){
		
		return count( $this->collection );
		
	}
    
	/**
	 * 执行方法
	 * @param string $name
	 * @param void... $arg
	 * @return multitype:mixed |mixed
	 */
	function __call($name,$arg){
		
		$mode = $this->callMode;
		$type = $this->type;
		
		if ($mode == self::CALL_ARRAY){
			
			$result = array();
			
			foreach ($this->collection as $item){
				
				if ( isset( $type ) && is_string( $type ) && !$item instanceof $type ) continue;
				
				$return = call_user_func_array( array($item , $name) ,$arg );
				
				$result[] = $return;
				
			}
			
			return $result;
			
		}
		
		
		
		
		if ($mode == self::CALL_TUNNEL) {
			
			$val = isset($arg[0])?$arg[0]:'';
			
			foreach ($this->collection as $item){
				
				if ( isset( $type ) && is_string( $type ) && !$item instanceof $type ) continue;
				
				
				if (isset($arg[0])) $arg[0] = $val;
				
				$return = call_user_func_array( array($item , $name) ,$arg );
				
				$val = $return;

				
			}
			
			return $val;
		}
		
		
		if( $mode == self::CALL_BOOL_STRICT ){
			
			$ready = array();
			
			foreach ($this->collection as $item){
				
				$return = false;
				
				if ( isset( $type ) && is_string( $type ) && !$item instanceof $type ) continue;
				
				$return = call_user_func_array( array($item , $name) ,$arg );
				
				if( !$return ){
					$this->error = get_class($item).':'.$name;
					
					if(!empty($ready)){
						foreach ($ready as $readyItem){
							
							if( $readyItem instanceof IInstanceSetStrict ){
								
								$readyItem->rollback($name);
								
							}
							
						}
					}
					
					return false;
				}
				
				$ready[] = $item;
				
			}
			
			return true;
			
		}
		
	}
	
	/**
	 * 在严格模式中，可以获取错误的类型
	 * @return string
	 */
	public function getError(){
		return $this->error;
	}
	
}