<?php

namespace App;

use App;

class Kernel 
{
    public $defaultControllerName = "BaseController";
    public $defaultActionName = "index";
    
/**
 * Парсит адресную строку и перенаправляет на действие, согласно полученным данным
 */    
    public function launch() 
    {
        
        list($controllerName, $actionName, $params) = App::$router->resolve();
        echo $this->launchAction($controllerName, $actionName, $params);
            
    }

/**
 * Парсит адресную строку и перенаправляет на указанное действие
 */        
    public function launchAction($controllerName, $actionName, $params) 
    {
        //$controllerName = empty($controllerName) ? $this->defaultControllerName : ucfirst($controllerName);
        $controllerName = $this->defaultControllerName;
        $filename = ROOTPATH.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.$controllerName.'.php';
        require_once $filename;
        
        $controllerName = "\\Controllers\\".ucfirst($controllerName);
        $controller = new $controllerName;
        
        $actionName = empty($actionName) ? $this->defaultActionName : $actionName;
        if (method_exists($controller, $actionName)){
            return $controller->$actionName($params);
        }
        
    }

}