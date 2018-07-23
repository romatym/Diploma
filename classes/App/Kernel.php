<?php

namespace App;

use App;

class Kernel {
    
    public $defaultControllerName = ProjectName;
    public $defaultActionName = "index";
    
    public function launch() {
        
        list($controllerName, $fileName, $actionName, $params) = App::$router->resolve();
        echo $this->launchAction($controllerName, $actionName, $params);
            
    }
    
    public function launchAction($controllerName, $actionName, $params) {
        
        $controllerName = empty($controllerName) ? $this->defaultControllerName : ucfirst($controllerName);
        $filename = ROOTPATH.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.$controllerName.'.php';
        //$filename = ROOTPATH.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR.$controllerName.'.php';
        //var_dump($filename);
        require_once $filename;
        
        $controllerName = "\\Controllers\\".ucfirst($controllerName);
        $controller = new $controllerName;
        $actionName = empty($actionName) ? $this->defaultActionName : $actionName;
        if (method_exists($controller, $actionName)){
            return $controller->$actionName($params);
        }
    }

}