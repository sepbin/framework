<?php
namespace Sepbin\System\Frame\Console;

use Sepbin\System\Core\Base;
use Sepbin\System\Util\IFactoryEnable;
use Sepbin\System\Core\IRouteEnable;
use Sepbin\System\Util\Factory;
use Sepbin\System\Util\Data\ClassName;
use Sepbin\System\Util\ConsoleUtil;

class ConsoleFrame extends Base implements IFactoryEnable, IRouteEnable
{
	
	
	private $sepService = [
		
		'Show','Clean','Add'
			
	];
	
	
	static public function getInstance( string $config_namespace=null, string $config_file=null, string $config_path=CONFIG_DIR ){
		
		if( $config_namespace == null ){
			$config_namespace = 'console';
		}
		
		return Factory::get(ConsoleFrame::class, $config_namespace, $config_file, $config_path);
		
	}
	
	
	/**
	 * 初始化，用于代替__construct方法
	 * 使用_init方法来根据配置初始化实例
	 */
	public function _init( \Sepbin\System\Util\FactoryConfig $config ){
		
		
		
	}
	
	
	
	public function RouteMapper( array $params ){
		
		$serviceName = $params['name'];
		$serviceName = ClassName::underlineToCamel($serviceName,true);
		
		if( in_array($serviceName, $this->sepService) ){
			$className = 'Sepbin\System\Service\Preset\\'.$serviceName;
		}else{
			$className = 'SepApp\Service\\'.$serviceName;
		}
		
		if( !class_exists($className) ){
			//类不存在
			ConsoleUtil::writeError( 'error: service '.$serviceName.' not found' );
			ConsoleUtil::writeError( 'please run command : show' );
			return ;
		}
		
		$instance = new $className;
		$action = request()->getStr('command','do').'Action';
		
		if( !method_exists($instance, $action) ){
			//方法不存在
			ConsoleUtil::writeError('error : service '. ClassName::camelToUnderline($serviceName) .' command '.request()->getStr('command','do').' not found ');
			
			
			
			$rclass = new \ReflectionClass($instance);
			$methods = $rclass->getMethods(\ReflectionMethod::IS_PUBLIC);
			
			$names = [];
			
			foreach ($methods as $item){
				if( substr($item->name, strlen($item->name)-6 ) == 'Action' ){
					$name = substr($item->name, 0, strlen($item->name)-6);
					if( $name == 'do' ){
						$names[] = ConsoleUtil::text('no command',-1,ConsoleUtil::COLOR_GREEN);
					}else{
						$names[] = $name;
					}
				}
			}
			
			ConsoleUtil::writeError('Usage : php sepbin '. ClassName::camelToUnderline($serviceName) .' {'.implode('|', $names).'}');
			
			return ;
		}
		
		$instance->$action();
		
	}
	
	
}