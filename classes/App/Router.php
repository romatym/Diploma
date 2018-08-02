<?php
namespace App;

class Router 
{
 /**
 * Парсит адресную строку: определяет адрес и  параметры запроса
 */    
    public function resolve () 
    {
        $params = NULL;
        $result = NULL;

        $route = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
        parse_str($query, $params);
        
        $result[0] = $route;
        $result[1] = empty($params['action']) ? NULL : $params['action'];
        $result[2] = $params;
        
        return $result;
        
    }
    
}