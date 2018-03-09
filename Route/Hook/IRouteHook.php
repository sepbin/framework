<?php
namespace Sepbin\System\Route\Hook;

interface IRouteHook
{
    
    
    public function routePath( string $path ) : string;
    
    public function routeHost( string $host ) : string;
    
    
}