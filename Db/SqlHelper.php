<?php 
namespace Sepbin\System\Db;


use Sepbin\System\Core\Base;


/**
 * SQL构建器
 * @author joson
 *
 */
class SqlHelper extends Base
{
	
	const ORDER_DESC = 'DESC';
	const ORDER_ASC = 'ASC';
	
	const JOIN = 'JOIN';
	const JOIN_LEFT = 'LEFT_JOIN';
	const JOIN_RIGHT = 'RIGHT JOIN';
	const JOIN_FULL = 'FULL JOIN';
	const JOIN_INNER = 'INNER JOIN';
	
	private $INSERT = 'INSERT INTO';
	private $UPDATE = 'UPDATE';
	private $SELECT = 'SELECT';
	private $DELETE = 'DELETE';
	
	
	/**
	 * 表名前缀
	 * @var string
	 */
	public $pre = '';
	
	private $table;
	
	private $type;
	
	private $params = array();
	
	private $queryCols = array();
	
	private $sets = array();
	
	private $order = array();
	
	private $join = '';
	
	private $joinOn = array();
	
	private $limit = '';
	
	private $wheres = '';
	
	private $group = '';
	
	private $having = array();
	
	private $lastSQL = '';
	
	/**
	 * 
	 * @var DbManager
	 */
	private $manager;
	
	
	static public function table(string $table){
		
		return new SqlHelper($table);
		
	}
	
	
	public function __construct( string $table ){
		
		$this->table = $table;
		
	}
	
	public function setManager( DbManager $manager ){
	    $this->manager = $manager;
	    return $this;
	}
	
	
	public function query( ...$cols ):SqlHelper{
		
		$this->type = $this->SELECT;
		
		if( count($cols) == 0 ){
			$this->queryCols = ['*'];
		}else{	
			$this->queryCols = $cols;
		}
		
		return $this;
		
	}
	
	public function insert( array $params = null ):SqlHelper{
		
		if( $params ){
			$this->params = array_merge($this->params,$params);
		}
		
		$this->type = $this->INSERT;
		return $this;
		
	}
	
	public function update( array $params = null ):SqlHelper{
		
		if( $params ){
			$this->params = array_merge($this->params,$params);
		}
		
		$this->type = $this->UPDATE;
		return $this;
		
	}
	
	public function delete():SqlHelper{
		
		$this->type = $this->DELETE;
		return $this;
		
	}
	
	public function put( string $name, $value ):SqlHelper{
		$this->params[$name] = $value;
		return $this;
	}
	
	public function where( ...$condition ):SqlHelper{
		
		if( count($condition) == 1 && $condition[0] instanceof SqlWhereHelper ){
			$this->wheres = $condition[0];
		}else{
			$this->wheres = $condition;
		}
		
		return $this;
	}
	
	public function order( string $name, string $sort = self::ORDER_DESC ):SqlHelper{
		
		$this->order[] = $name.' '.$sort;
		
		return $this;
		
	}
	
	public function join( $name, array $on, $type = self::JOIN ):SqlHelper{
		
		$this->joinOn = $on;
		$this->join = $type.' '.$this->pre.$name;
		
		return $this;
		
	}
	
	public function group( ...$name ):SqlHelper{
		
		$this->group = $name;
		
		return $this;
		
	}
	
	public function having( ...$condition ):SqlHelper{
		
		if( count($condition) == 1 && $condition[0] instanceof SqlWhereHelper ){
			$this->having = $condition[0];
		}else{
			$this->having = $condition;
		}
		
		return $this;
		
	}
	
	public function limit( int $offset, int $length=0 ):SqlHelper{
		
		if($length!=0){
			$this->limit = $offset. ',' .$length;
		}else{
			$this->limit = $offset;
		}
		
		return $this;
		
	}
	
	public function pre( string $pre ):SqlHelper{
		$this->pre = $pre;
		return $this;
	}
	
	public function get(){
		
		$this->lastSQL = '';
		
		switch ($this->type){
			case $this->SELECT:
				$this->createSelect();
				break;
			case $this->INSERT:
				$this->createInsert();
				break;
			case $this->UPDATE:
				$this->createUpdate();
				break;
			case $this->DELETE:
				$this->createDelete();
				break;
		}
		
		return $this->lastSQL;
		
	}
	
	public function exec(){
	    if( !$this->manager ) return null;
	    $this->lastSQL = '';
	    switch ($this->type){
	        case $this->INSERT:
	            $this->createInsert();
	            return $this->manager->insert($this->lastSQL);
	        case $this->UPDATE:
	            $this->createUpdate();
	            return $this->manager->update($this->lastSQL);
	        case $this->DELETE:
	            $this->createDelete();
	            return $this->manager->delete($this->lastSQL);
	    }
	}
	
	public function read(){
	    if( !$this->manager || $this->type != $this->SELECT ) return null;
	    $this->lastSQL = '';
	    $this->createSelect();
	    return $this->manager->read($this->lastSQL);
	}
	
	public function col(){
	    if( !$this->manager || $this->type != $this->SELECT ) return null;
	    $this->lastSQL = '';
	    $this->createSelect();
	    return $this->manager->col($this->lastSQL);
	}
	
	public function first(){
	    if( !$this->manager || $this->type != $this->SELECT ) return null;
	    $this->lastSQL = '';
	    $this->createSelect();
	    return $this->manager->first($this->lastSQL);
	}
	
	public function var(){
	    if( !$this->manager || $this->type != $this->SELECT ) return null;
	    $this->lastSQL = '';
	    $this->createSelect();
	    return $this->manager->var($this->lastSQL);
	}
	
	
	private function createInsert(){
		
		$this->lastSQL = $this->INSERT.' '.$this->getTableName() .'(';
		
		$this->lastSQL .= implode(',', array_keys($this->params));
		
		$this->lastSQL .= ') VALUES ('. implode(',', array_map([$this,'getValue'], array_values($this->params))).')';
		
	}
	
	
	
	private function createUpdate(){
		
		$this->lastSQL = $this->UPDATE.' '. $this->getTableName() .' SET ';
		$i = 0;
		foreach ($this->params as $key=>$val){
			$i++;
			$this->lastSQL .= $key.'='.$this->getValue($val);
			if( $i < count($this->params) ){
				$this->lastSQL .= ',';
			}
		}
		
		$this->appendWhere();
		
	}
	
	private function createDelete(){
		
		$this->lastSQL = $this->DELETE.' FROM '.$this->getTableName();
		
		$this->appendWhere();
		
	}
	
	
	
	/**
	 * 创建select语句
	 */
	private function createSelect(){
		
		$this->lastSQL = $this->SELECT.' '.implode(',', $this->queryCols);
		$this->lastSQL .= ' FROM '. $this->getTableName();
		
		if( $this->join != '' ){
			
			$this->lastSQL .= ' '.$this->join.' ON ';
			
			if($this->joinOn instanceof SqlWhereHelper){
				$where = $this->joinOn;
			}elseif ( is_array($this->joinOn) ){
				
				$where = SqlWhereHelper::create();
				foreach ($this->joinOn as $item){
					$where->put( ...$item );
				}
			}
			
			$this->lastSQL .= $where->get();
			
		}
		
		$this->appendWhere();
		
		
		if( $this->group != '' ){
			
			$this->group = array_map(function($str){
				if( strpos($str, '.') !== false ){
					return $this->pre.$str;
				}
				return $str;
			}, $this->group);
				
			$this->lastSQL .= ' GROUP BY '. implode(',', $this->group);
				
		}
		
		if( !empty($this->having) ){
			if( is_array($this->having) ){
				$where = SqlWhereHelper::create();
				foreach ($this->having as $item){
					$where->put( ...$item );
				}
			}else{
				$where = $this->having;
			}
			$this->lastSQL .= ' HAVING '.$where->get();
		}
		
		
		
		if(!empty($this->order)){
			
			$this->order = array_map(function($str){
				if( strpos($str, '.') !== false ){
					return $this->pre.$str;
				}
				return $str;
			}, $this->order);
				
				$this->lastSQL .= ' ORDER BY '.implode(',', $this->order);
		}
		
		
		if($this->limit != ''){
			$this->lastSQL .= ' LIMIT '.$this->limit;
		}
		
	}
	
	private function appendWhere(){
		
		if( $this->wheres != '' ){
			if( is_array($this->wheres) ){
				$where = SqlWhereHelper::create();
				foreach ($this->wheres as $item){
					$where->put( ...$item );
				}
			}else{
				$where = $this->wheres;
			}
			$this->lastSQL .= ' WHERE '.$where->get();
		}
		
	}
	
	public function getTableName( ){
		return $this->pre.$this->table;
	}
	
	private function getValue($value){
		
		if(is_numeric($value)){
			return $value;
		}
		
		if( !get_magic_quotes_gpc() ){
			$value = addslashes($value);
		}
		return "'$value'";
		
	}
	
	
	function __set($name,$value){
		
		$this->put($name, $value);
		
	}
	
}