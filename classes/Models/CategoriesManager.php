<?php

namespace Models;

class CategoriesManager 
{

    public function __construct() 
    {
    }

    public function getCategories($pdo) 
    {
        $sql = "SELECT id, name FROM categories";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();

        if (!$result) {
            return($stmt->errorInfo());
        }
        $table = [];
        foreach ($stmt->fetchAll() as $key => $value) {
            $table[$value['id']] = $value;
        }
        return $table;
    }

    public function addTopic($name) 
    {

        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $sql = "INSERT INTO categories (name) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$name]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    public function deleteTopic($id) 
    {

        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $sql = "delete from categories where id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    function SetCurrentCategory($Category_id) 
    {
        setcookie('category', $Category_id, time() + 3600);
    }

    function getCurrentCategoryFromCookies() 
    {

        if (isset($_COOKIE['category'])) {
            return $_COOKIE['category'];
        } else {
            return 1;
        }
    }

    function getCategoryByName($pdo, $name) 
    {

        $sql = "SELECT id, name FROM categories where name='" . $name . "'";
        $table = $pdo->query($sql);
        foreach ($table as $row) {
            return $row['id'];
        }
    }

}
