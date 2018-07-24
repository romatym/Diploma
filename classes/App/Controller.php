<?php

namespace App;

class Controller 
{
    public function render ($Template, array $params = []) 
    {
        
        try {
            // указывае где хранятся шаблоны
            $loader = new \Twig_Loader_Filesystem('templates');

            // инициализируем Twig
            $twig = new \Twig_Environment($loader);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate($Template);
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render($params);
        } catch (Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }        
        
    }
    
}