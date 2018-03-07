<?php
namespace Sepbin\System\Util;

class FileUtil
{
	
	/**
	 * 获取文件名称，不包含后缀名
	 * @param string $filename
	 * @return string
	 */
	static public function getName( string $filename ) : string{
		
	    return StringUtil::substrLastCharBefore( $filename , '.');
		
	}
	
	/**
	 * 获取文件后缀名
	 * @param string $filename
	 * @return string
	 */
	static public function getExtensionName( string $filename ) : string {
		
		return StringUtil::substrLastCharAfter($filename, '.');
		
	}
	
	
	/**
	 * 把一个路径和文件名合成一个完整的路径名
	 * @param string $filename
	 * @param string $path
	 * @return string
	 */
	static public function combineFullName( string $filename, string $path ) : string {
		
		if( StringUtil::substrLast($path) == '/' ){
			
			return $path. ltrim( $filename, '/' );
			
		}else{
			
			return $path. '/'. ltrim($filename,'/');
			
		}
		
	}
	
	
	/**
	 * 递归删除整个目录
	 * @param string $directory
	 */
	static public function rmdir(string $directory, $callback=null){
	    
		if(file_exists($directory)){
			if( false != ($dir_handle=@opendir($directory)) ){
				while( false != ($filename=readdir($dir_handle)) ){
				    
					if($filename!='.' && $filename!='..'){
						$subFile=$directory."/".$filename;
						if(is_dir($subFile)){
							self::rmdir($subFile, $callback);
						}
						if(is_file($subFile)){
						    $result = false;
						    if( is_writeable($subFile) ){
							    $result = unlink($subFile);
						    }
                            if( $callback && is_callable($callback) ){
                                $callback( $subFile, $result );
                            }
						}
					}
				}
				closedir($dir_handle);//关闭目录资源
				$result = false;
				if( is_writeable($directory) ){
				    $result = @rmdir($directory);//删除空目录
				}
				if( $callback && is_callable($callback) ){
				    $callback( $directory, $result );
				}
			}
		}
		
	}
	
}