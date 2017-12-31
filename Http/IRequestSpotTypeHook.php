<?php
namespace Sepbin\System\Http;

interface IRequestSpotTypeHook
{
	
	public function spot( string $request_type, Request $request ):string;
	
}