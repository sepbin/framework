<?php
namespace Sepbin\System\Core;

use Sepbin\System\Util\ArrayUtil;

class AppExceptionView extends Base
{
    
    /**
     * 
     * @var \Exception
     */
    static $err;
    
    static $app;
    
    static public function html(){
        
        ?>
        <html>
        <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        </head>
        <body style="margin:0; background:#efefef; font-size:12px;">
        	<div style="background:#f2f2f2; font-size:16px; color:#666; padding:10px; box-shadow:0 1px 10px #999;">
        		系统抛出异常
        	</div>
        	<div style="padding:10px;">
        		异常代码：<?php echo self::$err->getCode()?><br/>
        		错误信息：<?php echo self::$err->getMessage()?><br/>
        		<?php if( self::$app->isDebug() ):?>
        		文件位置：<?php echo self::$err->getFile()?><br/>
        		所在行数：<?php echo self::$err->getLine()?><br/>
        		
        		
        		<div style="padding:10px;background:#fefefe; margin-top:10px; border:1px solid #ccc;">
        		<table style="font-size:12px; width:100%">
        		<thead>
        		<tr>
        			<th align="left" style="border-bottom:1px solid #ccc;">调用方法</th>
        			<th align="left" style="border-bottom:1px solid #ccc;">文件</th>
        			<th align="left" style="border-bottom:1px solid #ccc;" width="50">行数</th>
        		</tr>
        		</thead>
        		<tbody>
        		<?php foreach (self::$err->getTrace() as $t):?>
        		<tr>
        			<td style="border-bottom:1px solid #ccc;">
        				<?php
                		echo $t['class'].$t['type'].$t['function'].'(';
                		
                		if (!empty($t['args'])){
                		    
                		    foreach ( $t['args'] as $key => $val ){
                		    	if ( is_array($val) ){
                		    		$t['args'][$key] = ArrayUtil::toExpString($val);
                		        }
                		        
                		        if( is_object($val) ){
                		        	$t['args'][$key] = '#'.get_class($val);
                		        }
                		        
                		    }
                		    
                		}
                		
                		foreach ($t['args'] as $k=>$v){
                			$t['args'][$k] = htmlspecialchars($v);
                		}
                		
                		echo implode(', ', $t['args']).')';
                		?>
        			</td>
        			<td style="border-bottom:1px solid #ccc;"><?php echo $t['file']?></td>
        			<td style="border-bottom:1px solid #ccc;"><?php echo $t['line']?></td>
        		</tr>
        		<?php endforeach;?>
        		</tbody>
        		</table>
        		</div>
        		<?php else:?>
        		<div style="padding:10px;background:#fefefe; margin-top:10px; border:1px solid #ccc;">
        			更多信息请打开调试模式查看
        		</div>
        		<?php endif;?>
        		
        	</div>
        </body>
        </html>
        <?php 
        
    }
    
    
    static public function json(){
    	
    	$msg = array(
    			'errcode' => self::$err->getCode(),
    			'message' => self::$err->getMessage()
    	);
    	
    	if( self::$app->isDebug() ){
	    	$msg = array_merge($msg, array(
	    			'file' => self::$err->getFile(),
	    			'line' => self::$err->getLine(),
	    			
	    	));
    	}
    	
    	echo json_encode( $msg );
    	
    }
    
    static public function string(){
    	
    	echo "----------------------------------------------\n";
    	echo "throw exception\n";
    	echo "----------------------------------------------\n";
    	echo "errcode:".self::$err->getCode()."\n";
    	echo "message:".self::$err->getMessage()."\n";
    	
    	if( self::$app->isDebug() ){
    		echo "file:".self::$err->getFile()."\n";
    		echo "line:".self::$err->getLine()."\n";
    	}
    	
    	echo "----------------------------------------------\n";
    	
    }
    
    
}