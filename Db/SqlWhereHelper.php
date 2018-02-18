<?php
namespace Sepbin\System\Db;

use Sepbin\System\Core\Base;


/**
 * SQL组装助手中WHERE语句的生成器
 * @author joson
 *
 */
class SqlWhereHelper extends Base
{
	
	const OP_LIKE = 'LIKE';
	
	const OP_IN = 'IN';
	
	const OP_NOT_IN = 'NOT IN';
	
	const OP_EXISTS = 'EXISTS';
	
	const OP_NOT_EXISTS = 'NOT EXISTS';
	
	const OP_EQ = '=';
	
	const OP_GT = '>';
	
	const OP_LT = '<';
	
	const OP_EGT = '>=';
	
	const OP_ELT = '<=';
	
	const RE_AND = 'AND';
	
	const RE_OR = 'OR';
	
	private $coder = array();
	
	
	/**
	 * 表名前缀
	 * @var string
	 */
	public $pre = '';
	
	
	static public function create(){
		
		return new SqlWhereHelper();
		
	}
	
	public function pre( string $pre ){
	    $this->pre = $pre;
	    return $this;
	}
	
	public function put( ...$params ){
		
		if( count($params) < 2 ) return $this;
		
		$name = $params[0];
		if( $name instanceof SqlWhereHelper ){
			
			if( empty($params[1]) ){
				$params[1] = self::RE_AND;
			}
			
			$this->coder[] = [ $name, $params[1] ];
			return $this;
			
		}
		
		$operator = isset( $params[2] ) ? $params[2] : '';
		$value = isset( $params[1] ) ? $params[1] : '';
		
		if( empty($params[3]) ){
			$params[3] = self::RE_AND;
		}
		
		if($operator == ''){
			
			if( is_array($value) ){
				$operator = self::OP_IN;
			}
			
			if( is_string($value) || is_numeric($value) || $value instanceof SqlTableName ){
				$operator = self::OP_EQ;
			}
			
			if( $value instanceof SqlHelper ){
				$operator = self::OP_IN;
			}
			
		}
		
		$this->coder[] = [ $name, $value, $operator, $params[3] ];
		
		return $this;
		
	}
	
	
	
	
	public function get(){
		
		$where = '';
		
		foreach ($this->coder as $item){
			
			if( count($item) == 4 ){
				
				$relation = $item[3];
				$name = $item[0];
				$value = $item[1];
				
				if( is_string($value) ){
					if( !get_magic_quotes_gpc() ){
						$value = addslashes($value);
					}
					$value = "'$value'";
				}
				
				
				if( is_array($value) ){
					
					$value = array_map(function($val){
							if( is_string($val) ){
								if( !get_magic_quotes_gpc() ){
									$val = addslashes($val);
								}
								$val = "'$val'";
							}
							return $val;
					}, $value);
					$value = '('.implode(',', $value).')';
					
				}
				
				if( $value instanceof SqlHelper ){
					if( $value->getAggregate() == '' ){
						$value = '('. $value->get() .')';
					}else{
						$value = $value->getAggregate().'('.$value->get().')';
					}
				}
				
				if( $value instanceof SqlTableName ){
					$value = $this->pre.$value->getName();
				}
				
				if( $name instanceof SqlTableName ){
					$name = $this->pre.$name->getName();
				}
				
				if( $name instanceof SqlHelper ){
					if( $name->getAggregate() == '' ){
						$name = '('. $name->get() .')';
					}else{
						$name = $name->getAggregate().'('.$name->get().')';
					}
				}
				
				$where .= $relation . ' ' . $name . ' ' . $item[2] . ' ' . $value.' ';
				
			}
			
			if( count($item) == 2 ){
				$where .= $item[1] .' ('.$item[0]->get().') ';
			}
			
		}
		
		$where = ltrim($where,'OR');
		$where = ltrim($where,'AND');
		
		return $where;
		
	}
	
}

class SqlTableName
{
	private $name;
	static public function get($name){
		return new SqlTableName($name);
	}
	function __construct($name){
		$this->name = $name;
	}
	public function getName(){
		return $this->name;
	}
}