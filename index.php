<?php
   
define('ROOTPATH', __DIR__);
//define('ProjectName', 'BaseController');

require_once ROOTPATH.DIRECTORY_SEPARATOR.'autoloader.php';

//ROOTPATH
App::init();
App::$kernel->launch();

