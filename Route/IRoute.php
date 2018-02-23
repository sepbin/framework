<?php
namespace Sepbin\System\Route;


interface IRoute
{
    
    public function addRoute($rule, $delegate, $params = array()) ;
    
    public function route();
    
}