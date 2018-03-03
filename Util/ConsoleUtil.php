<?php
namespace Sepbin\System\Util;

class ConsoleUtil{
	
	
	const COLOR_BLACK = 30;
	const COLOR_RED = 31;
	const COLOR_GREEN = 32;
	const COLOR_YELLOW = 33;
	const COLOR_BLUE = 34;
	const COLOR_PURPLE = 35;
	const COLOR_SKY_BLUE = 36;
	const COLOR_WHITE = 37;
	
	
	/**
	 * 获取输出文本
	 * @param string $str
	 * @param number $width
	 * @param number $color
	 * @param number $bg_color
	 * @return string
	 */
	static public function text( $str, $width=0, $color=0, $bg_color=0 ){
		
		$format = '%s';
		
		if( $width != 0 ){
			$format = '%-'.$width.'s';
		}
		
		if( $width == -1 ){
			$format = '%-'.strlen($str).'s';
		}
		
		if( $color != 0 ){
			
			if( $color < 30 || $color > 37 ) $color = 30;
			if( $bg_color >= 30 && $bg_color <= 37 ) $bg_color+=10;
			if( $bg_color != 0 && ($bg_color < 40 || $bg_color > 47) ) $bg_color = 40;
			if( $bg_color != 0 ) $bg_color.= ';';
			
			$format = "\033[{$bg_color}{$color}m$format\033[0m";
			
		}
		
		return sprintf($format, $str);
		
	}
	
	/**
	 * 获取输出横线
	 * @param number $width
	 * @return string
	 */
	static public function writeHorizontal( int $width=60 ){
		self::writeLine( self::getHorizontal($width) );
	}
	
	
	static public function getHorizontal( int $width=60 ){
		
		$str = '';
		for ($i=0;$i<$width;$i++){
			$str .= '-';
		}
		
		return $str;
		
	}
	
	/**
	 * 输出一个换行符
	 */
	static public function writeEnter(){
		fputs(STDOUT, "\n");
	}

	/**
	 * 输出字符
	 * @param string $str
	 */
	static public function write( string $str ){
		fputs(STDOUT, $str);
	}
	
	
	/**
	 * 输出一行字符
	 * @param string $str
	 */
	static public function writeLine( string $str ) {
		
		fputs(STDOUT, $str."\n");
		
	}
	
	
	/**
	 * 输出一个标准错误
	 * @param string $str
	 */
	static public function writeError( string $str ) {
		
		fputs(STDERR, self::text( $str, -1 , self::COLOR_RED )."\n");
		
	}
	
	static public function writeSuccess( string $str ){
		
		self::writeLine( self::text($str,0, self::COLOR_GREEN) );
		
	}
	
	
	/**
	 * 获取用户输入
	 * @param string $ask
	 * @return string
	 */
	static public function getInput( string $ask = '' ) : string {
		
		if( $ask != '' ) fputs(STDOUT, $ask);
		return trim(fgets(STDIN));
		
	}
	
	
	/**
	 * 获取一个必须填写的用户输入
	 * @param string $ask
	 * @return string
	 */
	static public function getRequireInput( string $ask = '', array $limit = [], $custom_check=null ):string{
		
		while ( true ){
			$answer = self::getInput( $ask );
			
			if( empty($answer) ){
				self::writeError('input can not be empty');
				continue;
			}
			if( !empty($limit) && !in_array($answer, $limit) ){
				self::writeError('the input must value must be {'.implode('|', $limit).'}');
				continue;
			}
			
			if( $custom_check != null && is_callable($custom_check) ){
				if( !$custom_check( $answer ) ) continue;
			}
			
			break;
		}
		
		return $answer;
		
	}
	
}