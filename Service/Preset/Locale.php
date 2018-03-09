<?php
namespace Sepbin\System\Service\Preset;

use Sepbin\System\Service\AbsService;
use Sepbin\System\Util\ConsoleUtil;
use Sepbin\System\Util\FileUtil;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Core\Exception\FileCantWriteException;
use Sepbin\System\Frame\Exception\ConsoleException;

/**
 *
 * @desc build locale file
 * @author joson
 *
 */
class Locale extends AbsService
{
	
    private $modules = [];
    
    private $styles = [];
    
    private $appLocal = APP_DIR.'/Local';
	
    private $viewLocal = PUBLIC_DIR.'/view';
    
    private $targetLang;
    
    private $autoTrans = false;
    
    function __construct(){
        
        $this->targetLang = request()->getStr('t');
        $this->autoTrans = request()->getBool('trans',false);
        
        if( empty($this->targetLang) ){
            throw (new ConsoleException())->appendMsg('The target language can not be empty');
        }
        
        if( !preg_match('/^[a-z]{2,3}_[A-Z]{2}$/', $this->targetLang) ){
            throw (new ConsoleException())->appendMsg('Language code format error，such as zh_CN');
        }
        
        ConsoleUtil::writeLine( 'You are generating '. $this->targetLang );
        
        $this->getModule();
        $this->getViewStyles();
        
    }
    
    public function doAction(){
        
        $this->sleepLot();
        $this->globalAction();
        $this->moduleAction();
        $this->viewAction();
        
    }
    
    
    public function globalAction(){
        
        $langs = [];
        $scandirs = [ APP_DIR, LIB_DIR ];
        foreach ($scandirs as $item){
            $langs = array_merge($langs, $this->scanDir($item, 'app') );
        }
        
        $po_file = APP_DIR.'/Locale/'.$this->targetLang.'/LC_MESSAGES/application.po';
        $this->createPo($langs, $po_file);
        
    }
    
    public function moduleAction(){
        
        foreach ($this->modules as $module){
            $langs = $this->scanDir(APP_DIR.'/Application/'.$module, 'module');
            if( empty($langs) ) continue;
            $po_file = APP_DIR.'/Locale/'.$this->targetLang.'/LC_MESSAGES/'. ClassName::camelToUnderline($module) .'.po';
            $this->createPo($langs, $po_file);
        }
        
    }
    
    
    public function viewAction(){
        
        foreach ($this->styles as $style){
            
            foreach ($this->modules as $module){
                $dir = $this->viewLocal.'/'.$style.'/'.ClassName::camelToUnderline($module);
                $langs = $this->scanDir($dir,'view') ;
                if(empty($langs)) continue;
                $po_file = $this->viewLocal.'/'.$style.'/locale/'.$this->targetLang.'/LC_MESSAGES/view_'. ClassName::camelToUnderline( $module ).'.po';
                $this->createPo($langs,$po_file);
            }
            
        }
        
    }
    
    
    private function createPo( $langs, $filename ){
        ConsoleUtil::writeHighlight('merge '. $filename);
        
        $need_trans = array_keys($langs);
        
        $createtime = date('Y-m-d H:iO');
        if( file_exists($filename) ){
            
            $poContent = file_get_contents($filename);
            $poContent = explode(PHP_EOL, $poContent);
            $oldLangs = [];
            for( $i=0; $i<count($poContent); $i++ ){
                if( empty($poContent[$i]) ) continue;
                if( preg_match('/^msgid "(.+)"$/', $poContent[$i], $matches) ){
                    $id = $matches[1];
                    $file = $line = '';
                    if( preg_match('/^#(.+):\s*(\d+)/', $poContent[$i-1], $matches) ){
                        if( !empty($matches[1]) ) $file = $matches[1];
                        if( !empty($matches[2]) ) $line = $matches[2];
                    }
                    
                    if( preg_match('/^msgstr "(.+)"$/', $poContent[$i+1], $matches) ){
                       $msg = $matches[1];
                       $i++;
                    }
                    if( !empty($id) && !empty($msg) ){
                        $oldLangs[$id] = [
                            'file' => $file,
                            'line' => $line,
                            'msg' => $msg
                        ];
                    }
                }
            }
            
            if( !empty($oldLangs) ){
                $surplus = array_diff( array_keys( $oldLangs ), array_keys( $langs ) );
                
                if( !empty($surplus) ){
                    ConsoleUtil::writeHighlight('Find out the excess in the old file, whether to delete them?');
                    $isDelete = ConsoleUtil::getRequireInput('enter [yes] or [no]?',['yes','no']);
                    if( $isDelete == 'yes' ){
                        foreach ($surplus as $item){
                            unset($oldLangs[$item]);
                        }
                    }
                }
                
                $need_trans = array_diff( $need_trans, array_keys($oldLangs) );
                $langs = array_merge($langs,$oldLangs);
            }
            
        }
        
        
        if( $this->autoTrans ){
            foreach ($need_trans as $k){
                $msg = $langs[$k]['msg'];
                $langs[$k]['msg'] = $this->trans($msg);
            }
            
        }
        
        
        $po = 'msgid ""
msgstr ""
"Project-Id-Version: sepbin\n"
"POT-Creation-Date: '.$createtime.'\n"
"Last-Translator: sepbin <hx@artsilk.cn>\n"
"Language-Team: sepbin\n"
"Language: en_US\n"
"MIME-Version: 1.0\n"
"Content-Type: text/plain; charset='.getApp()->charset.'\n"
    
';
        
        foreach ($langs as $msgid => $item){
            
            $po .= '
#'.$item['file'].':'.$item['line'].'
msgid "'.addcslashes($msgid,'\'"\\').'"
msgstr "'.addcslashes($item['msg'],'\'"\\').'"
            ';
        }
        
        $path = dirname($filename);
        if( !is_dir($path) ){
            if( !mkdir($path,0777, true) ){
                throw (new FileCantWriteException())->appendMsg($path);
            }
        }
        
        if( !is_writeable($path) ){
            throw (new FileCantWriteException())->appendMsg($path);
        }
        
        file_put_contents($filename, $po);
        ConsoleUtil::writeSuccess('Create success! '. $filename);
        
    }
    
    private function trans($msg){
        
        $gate = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        
        $appid = request()->getStr('appid');
        $key = request()->getStr('key');
        $lang = explode('_', $this->targetLang)[0];
        if($lang == 'fr') $lang = 'fra';
        if($lang == 'ar') $lang = 'ara';
        if($lang == 'es') $lang = 'spa';
        if($lang == 'ko') $lang = 'kor';
        if($lang == 'bg') $lang = 'bul';
        if($lang == 'et') $lang = 'est';
        if($lang == 'da') $lang = 'dan';
        if($lang == 'fi') $lang = 'fin';
        if($lang == 'ro') $lang = 'rom';
        if($lang == 'sl') $lang = 'slo';
        if($lang == 'sv') $lang = 'swe';
        if($lang == 'vi') $lang = 'vie';
        if($this->targetLang == 'zh_TW' || $this->targetLang == 'zh_HK') $lang = 'cht';
        
        $params['q'] = $msg;
        $params['from'] = 'auto';
        $params['to'] = $lang;
        $params['appid'] = '20180305000131419';
        $params['salt'] = mt_rand(0,9999);
        $params['sign'] = md5( $params['appid'].$params['q'].$params['salt'].$key );
        
        foreach ($params as $k=>$v){
            $params[$k] = urldecode($v);
        }
        
        $url = $gate . http_build_query($params);
        $result = file_get_contents($url);
        if( empty($result) ){
            ConsoleUtil::writeError('error : No response to translation');
            return $msg;
        }
        $result = json_decode($result,true);
        if( empty($result) ){
            ConsoleUtil::writeError('error : Incomplete data');
            return $msg;
        }
        if( !empty($result['error_code']) ){
            ConsoleUtil::writeError('error : Appid or key error');
            return $msg;
        }
        
        $result = $result['trans_result'][0]['dst'];
        ConsoleUtil::writeHighlight('trans : '.$result);
        return $result;
        
    }
    
    private function getModule(){
        
        ConsoleUtil::writeLine( 'Start scanning module.....' );
        $path = DOCUMENT_ROOT.'/application/Application';
        $dir = scandir( $path );
        
        foreach ($dir as $item){
            if( $item != '.' && $item != '..' && is_dir($path.'/'.$item) ){
                $this->modules[] = $item;
                ConsoleUtil::writeSuccess( 'find module : '. $item );
                $this->sleepLot();
            }
        }
        
    }
    
    private function getViewStyles(){
        ConsoleUtil::writeLine( 'Start scanning view styles.....' );
        
        $path = PUBLIC_DIR.'/view';
        
        $dir = scandir( $path );
        
        foreach ($dir as $item){
            if( $item != '.' && $item != '..' && is_dir($path.'/'.$item) ){
                $this->styles[] = $item;
                ConsoleUtil::writeSuccess( 'find style : '. $item );
                $this->sleepLot();
            }
        }
    }
    
    private function scanDir( string $directory, string $type){
        
        ConsoleUtil::writeLine('scanning '.$directory);
        
        $data = [];
        
        if(file_exists($directory)){
            
            if( false != ($dir_handle=@opendir($directory)) ){
                while( false != ($filename=readdir($dir_handle)) ){
                    
                    if($filename!='.' && $filename!='..'){
                        $subFile=$directory."/".$filename;
                        if(is_dir($subFile)){
                            $result = $this->scanDir($subFile, $type);
                            $data = array_merge($data,$result);
                        }
                        if(is_file($subFile)){
                            
                            $result = $this->parseFile($subFile, $type);
                            $data = array_merge( $data, $result );
                            
                            
                        }
                    }
                    
                    $this->sleepLot();
                    
                }
                closedir($dir_handle);
            }
        }else{
            
            ConsoleUtil::writeError('error: directory does not exist '.$directory);
            
        }
        
        return $data;
        
    }
    
    
    private function parseFile( $filename, $type ){
        
        $data = [];
        
        $ext = FileUtil::getExtensionName($filename);
        if( !in_array( $ext , ['html','phtml','php'] ) ){
            return $data;
        }
        
        ConsoleUtil::writeLine('parseFile : '. $filename);
        
        $contents = file_get_contents($filename);
        $contents = explode(PHP_EOL, $contents);
        
        $line = 1;
        
        foreach ($contents as $content){
            if(empty($content)) continue;
            
            if( $type == 'view' ){
                preg_match_all('/<t>(.+?)<\/t>/', $content, $matches);
                if(!empty($matches) && !empty($matches[1])){
                    $data = array_merge( $data, $this->getLangs( $matches[1], $filename, $line) ) ;
                }
            }
            
            
            if( $type == 'app' ){
                preg_match_all('/__t\(\s*(\'|")(.+)(\'|")\s*\)/', $content, $matches);
                if( !empty($matches) && !empty($matches[2]) ){
                    $data = array_merge( $data, $this->getLangs( $matches[2], $filename, $line) ) ;
                }
            }
            
            
            if( $type == 'module' ){
                preg_match_all('/\$this\->_t\(\s*(\'|")(.+)(\'|")\s*\)/', $content, $matches);
                if( !empty($matches) && !empty($matches[2]) ){
                    $data = array_merge( $data, $this->getLangs( $matches[2], $filename, $line) ) ;
                }
            }
            
            
            $line++;
        }
        
        
        return $data;
        
    }
    
    private function getLangs( $langs, $filename, $line ){
        $data = [];
        foreach ( $langs as $item ){
            $item = trim($item);
            $data[ $item ] = [
                'file' => substr( $filename, strlen(DOCUMENT_ROOT) ),
                'line' => $line,
                'msg' => $item
            ];
            ConsoleUtil::writeHighlight( 'find : '. $item .' in line '.$line);
        }
        return $data;
    }
    
    private function sleep(){
        usleep(500000);
    }
    
    private function sleepLot(){
        usleep(50000);
    }
	
}