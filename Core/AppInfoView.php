<?php
namespace Sepbin\System\Core;

class AppInfoView extends Base
{
    
    static $runtime;
    
    static $runmemory;
    
    
    static public function html(){
        
        
        
        ?>
        
        <div style="background:#fff; margin-top:10px; color:#666; font-size:12px; border-top:1px solid #eee; box-shadow:0 1px 10px #999; bottom:0; left:0; right:0">
        
            <div style=" background:#f2f2f2; padding:5px 10px; color:#000;">
            	时间：<?php echo self::$runtime; ?>s &nbsp;&nbsp;&nbsp;&nbsp;
            	内存：<?php echo self::$runmemory; ?>M &nbsp;&nbsp;&nbsp;&nbsp;
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
    
}