<?php

namespace App;

use App, PDO;

class Db 
{
    //public $pdo;
    
    public function __construct() 
    {
        $settings = $this->getPDOSettings();
        $this->pdo = new \PDO($settings['dsn'], $settings['user'], $settings['pass'], null);
    }
    
/**
 * Получает объект pdo для работы с базой данных
 */    
    static function pdo() {
        static $pdo = null;
        if ($pdo == null) {
            $settings = self::getPDOSettings();
            $pdo = new \PDO($settings['dsn'], $settings['user'], $settings['pass'], null);
        }
        return $pdo;
    }
    
/**
 * Возвращает настройки базы данных
 */    
    protected function getPDOSettings()
    {
        $config = include ROOTPATH.DIRECTORY_SEPARATOR.'Db'.DIRECTORY_SEPARATOR.'DbConfig.php';
        $result['dsn'] = "{$config['type']}:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $result['user'] = $config['user'];
        $result['pass'] = $config['pass'];
        return $result;       
    }
    
}