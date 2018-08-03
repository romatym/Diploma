<?php
   
define('ROOTPATH', __DIR__);

require_once ROOTPATH.DIRECTORY_SEPARATOR.'autoloader.php';

//ROOTPATH
App::init();
App::$kernel->launch();

