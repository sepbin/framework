<?php
namespace Sepbin\System\Db;

abstract class AbsSql
{
    
    
    const ORDER_DESC = 'DESC';
    const ORDER_ASC = 'ASC';
    
    const JOIN = 'JOIN';
    const JOIN_LEFT = 'LEFT_JOIN';
    const JOIN_RIGHT = 'RIGHT JOIN';
    const JOIN_FULL = 'FULL JOIN';
    const JOIN_INNER = 'INNER JOIN';
    
    protected $INSERT = 'INSERT INTO';
    protected $UPDATE = 'UPDATE';
    protected $SELECT = 'SELECT';
    protected $DELETE = 'DELETE';
    
    
    
    /**
     * 表名前缀
     * @var string
     */
    public $pre = '';
    
    protected $table;
    
    protected $type;
    
    protected $params = array();
    
    protected $queryCols = array();
    
    protected $sets = array();
    
    protected $order = array();
    
    protected $join = '';
    
    protected $joinOn = array();
    
    protected $limit = '';
    
    protected $wheres = '';
    
    protected $group = '';
    
    protected $having = array();
    
    protected $lastSQL = '';
    
    
    
    /**
     *
     * @var DbManager
     */
    private $manager;
    
    
    
    /**
     * 获取实例
     * @param string $table
     * @return AbsSql
     */
    static public function table(string $table){
        
        $name = static::class;
        return new $name($table);
        
    }
    
    
    
    public function __construct( string $table ){
        
        $this->table = $table;
        
    }
    
    
    
    public function setManager( DbManager $manager ){
        $this->manager = $manager;
        return $this;
    }
    
    
    
    
    
    
    
    
    /**
     * 设置查询内容
     * @param string ...$cols
     * @return AbsSql
     */
    public function query( ...$cols ):AbsSql{
        
        $this->type = $this->SELECT;
        
        if( count($cols) == 0 ){
            $this->queryCols = ['*'];
        }else{
            $this->queryCols = $cols;
        }
        
        return $this;
        
    }
    
    
    /**
     * 执行insert
     * @param array $params
     * @return AbsSql
     */
    public function insert( array $params = null ):AbsSql{
        
        if( $params ){
            $this->params = array_merge($this->params,$params);
        }
        
        $this->type = $this->INSERT;
        return $this;
        
    }
    
    /**
     * 执行update
     * @param array $params
     * @return AbsSql
     */
    public function update( array $params = null ):AbsSql{
        
        if( $params ){
            $this->params = array_merge($this->params,$params);
        }
        
        $this->type = $this->UPDATE;
        return $this;
        
    }
    
    
    /**
     * 执行删除
     * @return AbsSql
     */
    public function delete():AbsSql{
        
        $this->type = $this->DELETE;
        return $this;
        
    }
    
    
    
    
    
    
    
    
    
    
    public function getTableName( ){
        return $this->pre.$this->table;
    }
    
    
    public function pre( string $pre ):AbsSql{
        $this->pre = $pre;
        return $this;
    }
    
    
    
    public function put( string $name, $value ):AbsSql{
        $this->params[$name] = $value;
        return $this;
    }
    
    
    
    public function where( ...$condition ):AbsSql{
        
        if( count($condition) == 1 && $condition[0] instanceof SqlWhere ){
            $this->wheres = $condition[0];
        }else{
            $this->wheres = $condition;
        }
        
        return $this;
    }
    
    public function order( string $name, string $sort = self::ORDER_DESC ):AbsSql{
        
        $this->order[] = $name.' '.$sort;
        
        return $this;
        
    }
    
    
    
    
    
    
    public function join( $name, array ...$on ):AbsSql{
        
        $type = self::JOIN;
        
        if( count($on) > 0 && is_string( $on[ count($on) - 1 ] ) ){
            $type = $on[ count($on) - 1 ];
            unset($on[ count($on) - 1 ]);
        }
        
        $this->joinOn = $on;
        $this->join = $type.' '.$this->pre.$name;
        
        return $this;
        
    }
    
    public function group( ...$name ):AbsSql{
        
        $this->group = $name;
        
        return $this;
        
    }
    
    public function having( ...$condition ):AbsSql{
        
        if( count($condition) == 1 && $condition[0] instanceof SqlWhere ){
            $this->having = $condition[0];
        }else{
            $this->having = $condition;
        }
        
        return $this;
        
    }
    
    
    public function limit( int $offset, int $length=0 ):AbsSql{
        
        if($length!=0){
            $this->limit = $offset. ',' .$length;
        }else{
            $this->limit = $offset;
        }
        
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
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    /**
     * 执行
     * @return mixed
     */
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
        return null;
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
    
    
    
    protected function appendWhere( $wheres, $condition='WHERE' ){
        
        
        if( !empty($wheres) ){
            if( is_array($wheres) ){
                $where = $this->appendSubWhere($wheres);
            }else{
                $where = $wheres;
            }
            
            return ' '.$condition.' '.$where->get();
        }
        
        return '';
        
    }
    
    private function appendSubWhere( $wheres ){
        $where = SqlWhere::create()->pre($this->pre);
        foreach ( $wheres  as $item){
            if( is_array($item[0]) ){
                $where->put( $this->appendSubWhere( $item ) );
            }else{
                $where->put( ...$item );
            }
        }
        return $where;
    }
    
    
    
    
    
    abstract protected function createInsert();
    abstract protected function createUpdate();
    abstract protected function createDelete();
    abstract protected function createSelect();
    
    
    
    function __set($name,$value){
        
        $this->put($name, $value);
        
    }
    
    
    
}