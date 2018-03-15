<?php
namespace Sepbin\System\Db;

use Sepbin\System\Core\Base;


/**
 * SQL 的 WHERE语句的生成器
 * @author joson
 *
 */
class SqlWhere extends Base
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
        
        return new SqlWhere();
        
    }
    
    static public function tableVal($val,$alias=false){
        return new InnerSqlTableName($val,$alias);
    }
    
    public function pre( string $pre ){
        $this->pre = $pre;
        return $this;
    }
    
    public function put( ...$params ){
        
        if( count($params) < 1 ) return $this;
        
        $name = $params[0];
        if( $name instanceof SqlWhere ){
            
            if( empty($params[1]) ){
                $params[1] = self::RE_AND;
            }
            
            $this->coder[] = [ $name, $params[1] ];
            return $this;
            
        }
        
        $operator = isset( $params[2] ) ? $params[2] : '';
        $value = isset( $params[1] ) ? $params[1] : '';
        
        $split = self::RE_AND;
        
        if(  $operator  == self::RE_AND ||  $operator == self::RE_OR ){
            $split = $operator;
            $operator = '';
        }
        
        if( !empty($params[3]) ){
            $split = $params[3];
        }
        
        if($operator == ''){
            
            if( is_array($value) ){
                $operator = self::OP_IN;
            }
            
            if( is_string($value) || is_numeric($value) || $value instanceof InnerSqlTableName ){
                $operator = self::OP_EQ;
            }
            
            if( $value instanceof AbsSql ){
                $operator = self::OP_IN;
            }
            
        }
        
        $this->coder[] = [ $name, $value, $operator, $split ];
        
        return $this;
        
    }
    
    
    
    
    public function get(){
        
        $where = '';
        
        foreach ($this->coder as $item){
            
            if( count($item) == 4 ){
                
                $relation = $item[3];
                $name = $item[0];
                $value = $item[1];
                $oper = $item[2];
                
                
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
                
                if( $value instanceof AbsSql ){
                    if( $value->getAggregate() == '' ){
                        $value = '('. $value->get() .')';
                    }else{
                        $value = $value->getAggregate().'('.$value->get().')';
                    }
                }
                
                if( $value instanceof InnerSqlTableName ){
                    $value = $value->getName($this->pre);
                }
                
                if( $name instanceof InnerSqlTableName ){
                    $name = $name->getName($this->pre);
                }
                
                if( $name instanceof AbsSql ){
                    if( $name->getAggregate() == '' ){
                        $name = '('. $name->get() .')';
                    }else{
                        $name = $name->getAggregate().'('.$name->get().')';
                    }
                }
                
                $where .= $relation . ' ' . $name . ' ' . $oper . ' ' . $value.' ';
                
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

class InnerSqlTableName
{
    private $name;
    private $alias;
    static public function get($name, $alias){
        return new InnerSqlTableName($name, $alias);
    }
    function __construct($name, $alias){
        $this->name = $name;
        $this->alias = $alias;
    }
    public function getName($pre){
        if( !$this->alias ) return $pre.$this->name;
        return $this->name;
    }
}