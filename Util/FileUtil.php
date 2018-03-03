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
		
		return StringUtil::substrFirstCharBefore( $filename , '.');
		
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
	static public function rmdir(string $directory){
		if(file_exists($directory)){//判断目录是否存在，如果不存在rmdir()函数会出错
			if( false != ($dir_handle=@opendir($directory)) ){//打开目录返回目录资源，并判断是否成功
				while( false != ($filename=readdir($dir_handle)) ){//遍历目录，读出目录中的文件或文件夹
					if($filename!='.' && $filename!='..'){//一定要排除两个特殊的目录
						$subFile=$directory."/".$filename;//将目录下的文件与当前目录相连
						if(is_dir($subFile)){//如果是目录条件则成了
							self::rmdir($subFile);//递归调用自己删除子目录
						}
						if(is_file($subFile)){//如果是文件条件则成立
							unlink($subFile);//直接删除这个文件
						}
					}
				}
				closedir($dir_handle);//关闭目录资源
				rmdir($directory);//删除空目录
			}
		}
	}
	
}