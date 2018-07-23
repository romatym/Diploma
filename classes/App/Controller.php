<?php

namespace App;

class Controller {
    
    public function render ($Template, array $params = []) {
        
        try {
            // указывае где хранятся шаблоны
            $loader = new \Twig_Loader_Filesystem('templates');
            //var_dump($loader);

            // инициализируем Twig
            $twig = new \Twig_Environment($loader);
            //var_dump($twig);
            
            // подгружаем шаблон
            $template = $twig->loadTemplate($Template);
            //var_dump($template);
            
            // передаём в шаблон переменные и значения
            // выводим сформированное содержание
            echo $template->render($params);
            var_dump($template);
        } catch (Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }        
        
    }
    
}