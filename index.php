<?php
   
define('ROOTPATH', __DIR__);
define('ProjectName', 'Diploma');

//var_dump(file_exists(ROOTPATH.DIRECTORY_SEPARATOR.'autoloader.php'));
//var_dump(file_exists(ROOTPATH.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php'));

//require_once ROOTPATH.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require_once ROOTPATH.DIRECTORY_SEPARATOR.'autoloader.php';

//ROOTPATH
App::init();
App::$kernel->launch();

