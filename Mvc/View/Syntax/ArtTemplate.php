<?php
namespace Sepbin\System\Mvc\View\Syntax;

use Sepbin\System\Util\StringUtil;
use Sepbin\System\Mvc\View\BasicSyntax;


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
	
	private function parseVar(string $condition):string{
		
		return '$this->'.str_replace('.', '->',$condition);
		
	}
	
	private function parseCondition(string $condition):string{
		
		$condition = trim($condition);
		
		
		if( $condition == '/each'  ){
			return 'endforeach';
		}
		
		if( StringUtil::substrFirstLength($condition, 2) == 'if' ){
			
			$condition = substr($condition, 2);
			$condition = trim($condition);
			
			$condition =  $this->parseVars($condition);
			
			return "if( $condition ):";
		}
		
		if( $condition == '/if' ){
			return 'endif';
		}
		
		if( preg_match('/^(#|@)?[\w\.\[\]\'\"]+$/i', $condition) ){
			if(StringUtil::substrFirst($condition) == '#' || StringUtil::substrFirst($condition) == '@'){
				return 'echo '.$this->parseVar( substr($condition,1) );
			}
			
			return 'echo htmlspecialchars('.$this->parseVar($condition).')';
		}
		
		return '';
		
	}
	
	protected function parse(){
		
		
		$this->content = preg_replace_callback('/\{\{(.+?)\}\}/', function($matches){

			$item = $this->parseCondition($matches[1]);
			
			if(!empty($item)){
				$str = $this->phpTag( $item );
			}
			
			return $str;
			
		}, $this->content);
		
		
	}
	
	private function phpTag(string $str):string{
		
		return "<?php $str ?>";
		
	}
	
}