<?php

namespace Models;

class CategoriesManager 
{

    public function __construct() 
    {
    }

    public function getCategories($pdo) 
    {
        $sql = "SELECT categories.id, categories.name,
		count(DISTINCT questions.id) as numberOfQuestions,
		sum(questions.hidden) as numberOfQuestionsHidden,
		count(DISTINCT answers.id) as numberOfAnswers
		FROM categories
		LEFT JOIN questions ON questions.category_id = categories.id
		LEFT JOIN answers ON answers.question_id = questions.id
		group by categories.id";
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

        $sql = "DELETE FROM categories WHERE id = ?";

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

        $sql = "SELECT id, name FROM categories WHERE name = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$name]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        $table = $stmt->fetchAll();
        foreach ($table as $key => $value) {
            return $value['id'];
        }
    }

}
