<?php
namespace Sepbin\System\Core;

use Sepbin\System\Util\ConsoleUtil;

class AppInfoView extends Base
{
    
    static $runtime;
    
    static $runmemory;
    
    static $app;
    
    static public function html(){
        
        ?>
        
        <div style="background:#fff; position:fixed; z-index:999; margin-top:10px; color:#666; font-size:12px; border-top:1px solid #eee; box-shadow:0 1px 10px #999; bottom:0; left:0; right:0">
        
            <div style=" background:#f2f2f2; padding:5px 10px; color:#000;">
            	时间：<?php echo self::$runtime; ?>s &nbsp;&nbsp;&nbsp;&nbsp;
            	内存：<?php echo self::$runmemory; ?>M &nbsp;&nbsp;&nbsp;&nbsp;
            	文件：<?php echo count( get_included_files() )?>
            </div>
            
            <div style="padding:5px 10px; max-height:100px; overflow:auto">
            <?php if(!empty(AppInfo::$log)):?>
            	<?php foreach ( AppInfo::$log as $item ):?>
            
            	<div>
            		<?php echo $item->name?>
            		<?php echo $item->msg?>
            	</div>
            
            	<?php endforeach;?>
            <?php else:?>
            	运转良好，无任何警告
            <?php endif;?>
            </div>
        
        </div>
        
        
        <?php
        
    }
    
    static public function string(){
    	
    	putBuffer('');
    	putBuffer('');
    	putBuffer( ConsoleUtil::text('debug info',60,ConsoleUtil::COLOR_YELLOW, ConsoleUtil::COLOR_WHITE) );
    	putBuffer ( ConsoleUtil::text( "run time:".self::$runtime."s  memory:".self::$runmemory."M", 60, ConsoleUtil::COLOR_BLACK, ConsoleUtil::COLOR_WHITE ) );
    	putBuffer('');
    	if( !empty(AppInfo::$log) ){
	    	foreach ( AppInfo::$log as $item ){
	    	    putBuffer( $item->msg."\n" );
	    	}
    	}else{
    	    putBuffer('good. no errors');	
    	}
    	putBuffer('');
    	putBuffer( ConsoleUtil::text('sepbin version '.getApp()->version,60,ConsoleUtil::COLOR_YELLOW, ConsoleUtil::COLOR_WHITE) );
    	
    }
    
    static public function data(){
    	
    	$data = [ 'debug_info' => [] ];
    	
    	$data['debug_info']['run_time'] = self::$runtime;
    	$data['debug_info']['memory'] = self::$runmemory.'M';
    	$data['debug_info']['warnings'] = [];
    	foreach ( AppInfo::$log as $item ){
    		$data['debug_info']['warnings'][] = $item->msg;
    	}
        
    	putBuffer($data);
    	
    }
    
}