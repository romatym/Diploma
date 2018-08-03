<?php

function autoloadVendor($className) 
{
    $filePath = ROOTPATH . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }
}

function autoload($className) 
{
    $filePath = ROOTPATH . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $className . '.php';
    $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $filePath);
    if (file_exists($filePath)) {
        include "$filePath";
    }
}

function autoloadModels($className) 
{
    $filePath = ROOTPATH . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Models' . $className . '.php';
    $filePath = str_replace('\\', DIRECTORY_SEPARATOR, $filePath);
    if (file_exists($filePath)) {
        include "$filePath";
    }
}

function autoloadApp($className) 
{
    $filePath = ROOTPATH . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . $className . '.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }
}

function autoloadControllers($className) 
{
    $filePath = ROOTPATH . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $className . '.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }
}

function autoloadDb($className) 
{
    $filePath = ROOTPATH . 'classes' . DIRECTORY_SEPARATOR . 'Db' . DIRECTORY_SEPARATOR . $className . '.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }
}

spl_autoload_register('autoloadVendor');
spl_autoload_register('autoloadApp');
spl_autoload_register('autoloadControllers');
spl_autoload_register('autoloadModels');
spl_autoload_register('autoload');
