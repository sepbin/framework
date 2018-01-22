<?php
namespace Sepbin\System\Frame\Mvc\View\Syntax;

use Sepbin\System\Util\StringUtil;
use Sepbin\System\Frame\Mvc\View\BasicSyntax;
use Sepbin\System\Frame\Mvc\Exception\SyntaxException;


/**
 * artTemplate.js 语法
 * 这里要注意的是，我们只实现基本的输出、判断、循环语句
 * 请谨遵sepbin的原则，Model返回的数据一定是可用的最终形态，因为Model返回时等于已经公开
 * 这是因为前端和后端的运行环境不同，比如前端有的函数后端没有，造成模板不能共用
 * 
 * @author joson
 *
 */
class ArtTemplate extends BasicSyntax
{
	
	/**
	 * 关键词
	 * @var array
	 */
	private $keyword = ['true','false'];
	
	
	/**
	 * 循环级数
	 * @var integer
	 */
	private $loopLevel = 0;
	
	/**
	 * 判断级数
	 * @var integer
	 */
	private $ifLevel = 0;
	
	
	
	/**
	 * 在一串表达式里，找到为变量的字符，并转义成PHP变量表达式
	 * @param string $condition
	 * @return string
	 */
	private function parseVars( string $condition ):string{
		
		$strs = array();
		
		$condition = preg_replace_callback('/(\'|\").*?(\'|\")/', function($matches)use(&$strs){
			
			$strs[] = $matches[0];
			return '\'"***"\'';
			
		}, $condition);
		
		
		$condition = preg_replace_callback('/[\w\.]+/', function($matches){
			
			$item = $matches[0];
			if( !is_numeric($item) && !in_array($item, $this->keyword) ){
				return $this->parseVar($item) ;
			}else{
				return $item;
			}
			
		}, $condition);
		
		
		$i = -1;
		$condition = preg_replace_callback('/\\\'"\*\*\*"\\\'/', function($matches) use ($strs,&$i){
			$i++;
			return $strs[$i];
		}, $condition);
		
		
		return $condition;
		
	}
	
	
	
	
	/**
	 * 转义变量表达式
	 * @param string $condition
	 * @return string
	 */
	private function parseVar(string $condition):string{
		
		
		if( $this->loopLevel > 0 ){
			return $this->parsePrivateVar($condition);
		}
		
		
		return '$this->'.str_replace('.', '->',$condition);
		
	}
	
	
	/**
	 * 转义局部变量表达式
	 * 在php里，顶级变量是$this->xx  局部变量是$xx
	 * @param string $condition
	 * @return string
	 */
	private function parsePrivateVar( string $condition  ):string{
		return '$'.str_replace('.', '->',$condition);
	}
	
	
	
	/**
	 * 转义表达式
	 * @param string $condition
	 * @return string
	 */
	private function parseCondition(string $condition):string{
		
		$sourceCondition = $condition;
		$condition = trim($condition);
		
		if( StringUtil::substrFirstLength($condition, 4) == 'each' ){
			
			$condition = substr($condition, 4);
			$condition = trim($condition);
			
			if( preg_match('/^\w+$/', $condition) ){
				
				$result = '//if(isset('.$this->parseVar( $condition ).')):
					foreach( '. $this->parseVar( $condition ) .' as $index => $value ):
					if( is_array($value) ) $value= json_decode( json_encode( $value ) );
					';
				
			}
			
			$tmparr = preg_split('/\s+/', $condition);
			if( count($tmparr) == 4 && $tmparr[1] == 'as' ){
				
				$result = '
					foreach( '. $this->parseVar( $tmparr[0] ).' as $'.$tmparr[3].' => $'.$tmparr[2].' ):
					if( is_array($'.$tmparr[2].') ) $'.$tmparr[2].'= json_decode( json_encode( $'.$tmparr[2].' ) );
				';
				
			}
			
			
			if(!isset($result)){
				throw ( new SyntaxException() )->appendMsg('each 语法错误 片段 '.$sourceCondition);
			}
			
			$this->loopLevel++;
			return $result;
			
		}
		
		
		if( $condition == '/each'  ){
			
			$this->loopLevel--;
			return 'endforeach;';
			
		}
		
		if( StringUtil::substrFirstLength($condition, 2) == 'if' ){
			
			$this->ifLevel++;
			$condition = substr($condition, 2);
			$condition = trim($condition);
			
			$condition =  $this->parseVars($condition);
			
			return "if( $condition ):";
		}
		
		if( $condition == '/if' ){
			$this->ifLevel--;
			return 'endif';
		}
		
		if( preg_match('/^(#|@|\$)?[\w\.\[\]\'\"]+$/i', $condition) ){
			if(StringUtil::substrFirst($condition) == '#' || StringUtil::substrFirst($condition) == '@'){
				return 'echo '.$this->parseVar( substr($condition,1) );
			}
			
			if(StringUtil::substrFirst($condition) == '$'){
				return 'echo '.$this->parsePrivateVar( substr($condition,1) );
			}
			
			return 'echo htmlspecialchars('.$this->parseVar($condition).')';
			
		}
		
		
		throw ( new SyntaxException() )->appendMsg('语法错误 片段 '.$sourceCondition);
		
	}
	
	
	/**
	 * 转义入口方法
	 * {@inheritDoc}
	 * @see \Sepbin\System\Mvc\View\BasicSyntax::parse()
	 */
	protected function parse(){
		
		$this->content = preg_replace_callback('/\{\{(.+?)\}\}/', function($matches){

			$item = $this->parseCondition($matches[1]);
			
			if(!empty($item)){
				return $this->phpTag( $item );
			}
			
			return '';
			
		}, $this->content);
		
		if( $this->loopLevel > 0 ){
			throw (new SyntaxException())->appendMsg('循环没有正常结束，缺少{{/each}}');
		}
		
		if( $this->ifLevel > 0 ){
			throw (new SyntaxException())->appendMsg('if没有正常结束，缺少{{/if}}');
		}
		
	}
	
	
	
}