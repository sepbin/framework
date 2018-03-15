<?php 
namespace Sepbin\System\Db\Sql;



use Sepbin\System\Db\AbsSql;
use Sepbin\System\Db\SqlWhere;

/**
 * MYSQL 的 SQL构建器
 * @author joson
 *
 */
class StandardSql extends AbsSql
{
	
    
	
	protected function createInsert(){
		
		$this->lastSQL = $this->INSERT.' '.$this->getTableName() .'(';
		
		$this->lastSQL .= implode(',', array_keys($this->params));
		
		$this->lastSQL .= ') VALUES ('. implode(',', array_map([$this,'getValue'], array_values($this->params))).')';
		
	}
	
	
	
	protected function createUpdate(){
		
		$this->lastSQL = $this->UPDATE.' '. $this->getTableName() .' SET ';
		$i = 0;
		foreach ($this->params as $key=>$val){
			$i++;
			$this->lastSQL .= $key.'='.$this->getValue($val);
			if( $i < count($this->params) ){
				$this->lastSQL .= ',';
			}
		}
		
		$this->lastSQL.= $this->appendWhere( $this->wheres );
		
	}
	
	protected function createDelete(){
		
		$this->lastSQL = $this->DELETE.' FROM '.$this->getTableName();
		
		$this->lastSQL.=$this->appendWhere( $this->wheres );
		
	}
	
	
	
	/**
	 * 创建select语句
	 */
	protected function createSelect(){
		
		$this->lastSQL = $this->SELECT.' '.implode(',', $this->queryCols);
		$this->lastSQL .= ' FROM '. $this->getTableName();
	    
		if( $this->join != '' ){
			
			$this->lastSQL .= ' '.$this->join;
			
			$this->lastSQL .= $this->appendWhere($this->joinOn, 'ON');

			
		}
		
		$this->lastSQL.=$this->appendWhere( $this->wheres );
		
		
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
				$where = SqlWhere::create()->pre($this->pre);
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
	
	
	
	
	
	
	
	
	private function getValue($value){
		
		if(is_numeric($value)){
			return $value;
		}
		
		$value = str_replace("'", "''", $value);
		
		return "'$value'";
		
	}
	
	
	
	
}