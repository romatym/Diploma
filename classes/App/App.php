<?php

class App {
        
    public static $router;
    public static $db;
    public static $kernel;
    public static $Controller;
    
    public static function init()
    {
        static::bootstrap();
        
    }
    
    public static function bootstrap() {
        static::$router = new App\Router();
        static::$kernel = new App\Kernel();
        static::$Controller = new App\Controller();
        static::$db = new App\Db();
       
    }
    
}