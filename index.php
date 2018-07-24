<?php
   
define('ROOTPATH', __DIR__);
define('ProjectName', 'Diploma');

require_once ROOTPATH.DIRECTORY_SEPARATOR.'autoloader.php';

//ROOTPATH
App::init();
App::$kernel->launch();

