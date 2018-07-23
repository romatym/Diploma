<?php

function AutoloadVendor($className) {
    $filePath = ROOTPATH.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }  
}
function Autoload($className) {
    $filePath = 'classes' .DIRECTORY_SEPARATOR. $className . '.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }  
}
function AutoloadApp($className) {
    $filePath = ROOTPATH.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'App' .DIRECTORY_SEPARATOR. $className . '.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }  
}
function AutoloadControllers($className) {
    $filePath = 'classes'.DIRECTORY_SEPARATOR.'Controllers'.DIRECTORY_SEPARATOR. $className . '.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }  
}
function AutoloadDb($className) {
    $filePath = 'classes'.DIRECTORY_SEPARATOR.'Db'.DIRECTORY_SEPARATOR. $className . '.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }  
}
function AutoloadForum($className) {
    $filePath = 'classes'.DIRECTORY_SEPARATOR.'Forum'.DIRECTORY_SEPARATOR. $className . '.php';
    if (file_exists($filePath)) {
        include "$filePath";
    }  
}

spl_autoload_register('AutoloadVendor');
spl_autoload_register('AutoloadApp');
spl_autoload_register('AutoloadControllers');
spl_autoload_register('AutoloadForum');
spl_autoload_register('Autoload');
