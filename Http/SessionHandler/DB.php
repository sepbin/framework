<?php
namespace Sepbin\System\Http\SessionHandler;

use Sepbin\System\Util\Factory;
use Sepbin\System\Db\DbManager;

class DB extends Base
{
    
    private $db;
    
    private $table;
    
    private $colSessionID;
    
    private $colSessionData;
    
    private $colUpdatetime;
    
    
    static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ){
        
        return Factory::get(DB::class, $config_namespace, $config_file, $config_path);
        
    }
    
    public function _init( \Sepbin\System\Util\FactoryConfig $config ){
        
        $dbname = $config->getStr('db_config_name');
        $this->db = DbManager::getInstance($dbname);
        
        $this->table = $config->get('table_name');
        $this->colSessionID = $config->getStr('col_session_id');
        $this->colSessionData = $config->getStr('col_session_data');
        $this->colUpdatetime = $config->getStr('col_updatetime');
        
    }
    
    public function open($save_path, $session_name){
        
        return true;
        
    }
    
    public function close () {
        
        return true;
        
    }
    
    
    public function write($session_id, $session_data){
        
        $check = $this->db->getSQL($this->table)->query($this->colSessionID)->var();
        
        if(empty($check)){
            $this->db->getSQL($this->table)
            ->insert([ $this->colSessionID => $session_id, $this->colSessionData => $session_data, $this->colUpdatetime => time() ])
            ->exec();
        }else{
            $this->db->getSQL($this->table)
            ->update([ $this->colSessionData => $session_data, $this->colUpdatetime => time() ])
            ->where( [$this->colSessionID , $session_id] )->exec();   
        }
        
    }
    
    
    public function read ($session_id) {
        
        $result = $this->db->getSQL($this->table)->query( $this->colSessionData )->where([$this->colSessionID, $session_id])->var();
        
        return $result;
        
    }
    
    
    public function destroy ($session_id) {
        
        $this->db->getSQL($this->table)->where([$this->colSessionID,$session_id])->delete()->exec();
        
        return true;
        
    }
    
    
    public function gc ($maxlifetime) {
        
        $this->db->getSQL($this->table)->where( [$this->colUpdatetime, time()-$maxlifetime, '<' ] )->delete()->exec();
        return true;
        
    }
    
}