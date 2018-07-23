<?php
namespace App;

class Router {
    
    public function resolve () {
        
        $route = NULL;
        if(($pos = strpos($_SERVER['REQUEST_URI'], '?')) !== false) {
            $route = substr($_SERVER['REQUEST_URI'], 0, $pos);
        }
        $route = is_null($route) ? $_SERVER['REQUEST_URI'] : $route;
        $route = stristr($route, ProjectName);
        //var_dump($route);
        
        $route = explode('/', $route);
        //array_shift($route);
        //array_shift($route);
        $result[0] = array_shift($route);
        $result[1] = array_shift($route);
        $result[2] = array_shift($route);
        $result[3] = $route;
        
        return $result;
        
    }
    
}