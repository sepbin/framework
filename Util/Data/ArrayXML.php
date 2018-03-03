<?php
namespace Sepbin\System\Util\Data;

class ArrayXML
{
	
	static public function arrayToXmlString( array $arr ){
		
		$str = '<xmlData></xmlData>';
		
		$xmlDoc = new \SimpleXMLElement($str);
		
		foreach($arr as $key => $val){
			if(is_array($val)){
				$child = $xmlDoc->addChild($key);
				self::arrayToXmlString($val, $child);
			}else{
				$xmlDoc->addChild($key, $val);
			}
		
		}
		
		return $xmlDoc->asXML();
		
	}
	
}