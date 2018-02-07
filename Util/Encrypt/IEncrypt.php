<?php
namespace Sepbin\System\Util\Encrypt;

use Sepbin\System\Util\IFactoryEnable;

interface IEncrypt extends IFactoryEnable
{
	
	public function encrypt( string $data ):string;
	
	public function decrypt( string $data ):string;
	
}