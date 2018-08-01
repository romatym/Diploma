<?php

namespace Models;

class QuestionsManager 
{

    public function __construct() 
    {
    }

    public function addQuestion($category, $name, $email, $topic, $question) 
    {

        $pdo = \App\Db::pdo();

        $categoriesManager = new CategoriesManager();
        $categoryId = $categoriesManager->getCategoryByName($pdo, $category);

        $sql = "INSERT INTO questions (category_id, author, email, topic, question, date) VALUES (?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$categoryId, $name, $email, $topic, $question, date("Y-m-d H:i:s")]);

        if (!$result) {
            return $stmt->errorInfo();
        }
        return $result;
    }

    public function getQuestions($hidden) 
    {
        $pdo = \App\Db::pdo();
        $sql = "SELECT questions.id, questions.question, questions.category_id, questions.topic, questions.date, questions.email, questions.hidden, questions.author,
                admins.login,
                categories.name AS category,
            	count(DISTINCT answers.id) AS answersNumber
                FROM questions
                LEFT JOIN admins ON admins.id = questions.admin_id
                LEFT JOIN categories ON categories.id = questions.category_id
                LEFT JOIN answers ON answers.question_id = questions.id
                
                WHERE (NOT questions.hidden OR ? )
                
                group by questions.id
                ";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([!$hidden]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }
    
    public function getQuestionsWithoutAnswers() 
    {
        $pdo = \App\Db::pdo();
        $sql = "SELECT questions.id, questions.question, questions.category_id, questions.topic, 
                questions.date, questions.email, questions.hidden, questions.author,
                COUNT(DISTINCT answers.id) AS answersNumber
                FROM questions
                LEFT JOIN answers ON answers.question_id = questions.id
                GROUP BY questions.id
                HAVING answersNumber=0
                ";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();
        
        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }

    public function getTreeOfQuestionsAndAnswers($Categories, $Questions, $Answers) 
    {
        $tree = [];

        foreach ($Categories as $key1 => $value1) {
            $branchQuestions = [];

            foreach ($Questions as $key2 => $value2) {
                if ($value1['id'] == $value2['category_id']) {
                    $branchQuestions[] = $value2;
                }
            }
            $tree[$value1['id']] = $branchQuestions;
        }

        return $tree;
    }

    function getQuestionById($questionId) 
    {
        $pdo = \App\Db::pdo();
        $sql = "SELECT questions.id, topic, email, category_id, author, question, categories.name AS category
                FROM questions
                LEFT JOIN categories ON categories.id = questions.category_id
                WHERE questions.id= ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$questionId]);
        
        $table = $stmt->fetchAll();
        foreach ($table as $key => $value) {
            return $value;
        }
    }

    public function deleteQuestion($id) 
    {
        $pdo = \App\Db::pdo();
        //удаляем вопрос
        $sql = "DELETE FROM questions WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        //удаляем ответы на вопрос
        $sql = "DELETE FROM answers WHERE question_id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    public function changeHidden($id, $hiddenNewValue) 
    {
        $pdo = \App\Db::pdo();
        $sql = "UPDATE questions SET hidden = ? WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$hiddenNewValue, $id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        return TRUE;
    }

    public function changeQuestion($id, $topic, $text) 
    {
        $pdo = \App\Db::pdo();
        $sql = "UPDATE questions SET topic = ?, question = ? WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$topic, $text, $id]);

        if (!$result) {
            return($stmt->errorInfo());
        }

        return TRUE;
    }
}
