<?php
namespace Sepbin\System\Util;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\Exception\InstanceSetTypeException;

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
	 * 如果实例实现了\Sepbin\Core\IInstanceSetArray接口，则还会调用返回false实例的rollback方法
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
	 * 不返回结果
	 * @var integer
	 */
	const CALL_VOID = 4;
	
	
	/**
	 * 当前执行模式
	 * @var integer
	 */
	private $callMode = self::CALL_VOID;
	
	
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
			throw (new InstanceSetTypeException())->appendMsg('需要 '.$this->type);
		}
		
		$this->collection[] = $instance;
		
	}
	
	
	/**
	 * 设置模式
	 * @param const $mode
	 * @return \Sepbin\Core\InstanceSet
	 */
	public function setMode($mode) : InstanceSet{
		
		$this->callMode = $mode;
		
		return $this;
	}
	
	/**
	 * 得到索引所指向的一个实例
	 * @param int $index
	 */
	public function getIndex( int $index){
		
		if ( isset($this->collection[$index]) ){
			return $this->collection[$index];
		}
		
		return null;
	}
	
	
	/**
	 * 得到集合的长度
	 * @return int
	 */
	public function getLength():int{
		
		return count( $this->collection );
		
	}
    
	
	
	private function checkType( $item ) : bool{
		
		return isset( $this->type ) && is_string( $this->type ) && !$item instanceof $this->type;
		
	}
	
	
	private function callArray( string $name, array $arg ) : array {
		
		$result = array();
		
		foreach ($this->collection as $item){
			
			if ( $this->checkType($item) ) continue;
			
			$return = $item->$name( ...$arg );
			
			if( $return === false && $item instanceof IInstanceSetArray ){
				$item->rollback($name);
			}
			
			$result[] = $return;
			
		}
		
		return $result;
		
	}
	
	
	
	private function callTunnel( string $name, array $arg ) {
		
		$val = isset($arg[0])?$arg[0]:'';
		
		foreach ($this->collection as $item){
			
			if ( $this->checkType($item) ) continue;
			
			if (isset($arg[0])) $arg[0] = $val;
			
			$return = $item->$name(...$arg);
			
			$val = $return;
			
			
		}
		
		return $val;
		
	}
	
	private function callBoolStrict( string $name, array $arg ) : bool{
		
		$ready = array();
		
		foreach ($this->collection as $item){
			
			$return = false;
			
			if ( $this->checkType($item) ) continue;
			
			$return = $item->$name(...$arg);
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
	
	
	private function callVoid( string $name, array $arg ):void{
		
		foreach ($this->collection as $item){
			
			if ( $this->checkType($item) ) continue;
			$return = $item->$name(...$arg);
			
		}
		
	}
	
	/**
	 * 执行方法
	 * @param string $name
	 * @param void... $arg
	 * @return multitype:mixed |mixed
	 */
	function __call($name,$arg){
		
		if( substr($name, 0, 1) != '_' ){
			return ;
		}
		
		$name = substr($name, 1);
		
		if ( $this->callMode == self::CALL_ARRAY ){
			
			return $this->callArray($name, $arg);
			
		}
		
		
		if ( $this->callMode == self::CALL_TUNNEL) {
			
			return $this->callTunnel($name, $arg);
			
		}
		
		
		if( $this->callMode == self::CALL_BOOL_STRICT ){
			
			return $this->callBoolStrict($name, $arg);
			
		}
		
		if( $this->callMode == self::CALL_VOID ){
			
			$this->callVoid($name, $arg);
			
		}
		
	}
	
	/**
	 * 在严格模式中，可以获取错误的类型
	 * @return string
	 */
	public function getError():string{
		return $this->error;
	}
	
}