<?php
namespace Sepbin\System\Core\Hook;

interface IRequestSpotTypeHook
{
	
	public function spot( string $request_type, \Sepbin\System\Core\Request $request ):string;
	
}