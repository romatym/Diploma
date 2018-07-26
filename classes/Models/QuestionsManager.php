<?php

namespace Models;

class QuestionsManager 
{

    public function __construct() 
    {
    }

    public function addQuestion($category, $name, $email, $topic, $question) 
    {

        $Db = new \App\Db();
        $pdo = $Db->pdo;

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

    public function getQuestions($pdo, $hidden) 
    {

        $whereCondition = $hidden ? " where not questions.hidden" : "";

        $sql = "SELECT questions.id, questions.question, questions.topic, questions.date, questions.email, questions.hidden, questions.author,
            admins.login,
            questions.category_id, categories.name as category
            FROM questions
            LEFT JOIN admins on admins.id = questions.admin_id
            LEFT JOIN categories on categories.id = questions.category_id
            " . $whereCondition;

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }

    public function getTreeOfQuestionsWithAnswers($Categories, $Questions, $Answers) 
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

    function getQuestionById($pdo, $questionId) 
    {

        $sql = "SELECT questions.id, topic, email, category_id, author, question, categories.name as category
                FROM questions
                LEFT JOIN categories on categories.id = questions.category_id
                where questions.id='" . $questionId . "'";
        $table = $pdo->query($sql);
        foreach ($table as $row) {
            return $row;
        }
    }

    public function deleteQuestion($id) 
    {

        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $sql = "delete from questions where id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    public function changeHidden($id, $hiddenNewValue) 
    {

        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $sql = "UPDATE questions SET hidden = ? where id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$hiddenNewValue, $id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        return TRUE;
    }

    public function changeQuestion($id, $topic, $text) 
    {

        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $sql = "UPDATE questions SET topic = ?, question = ? where id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$topic, $text, $id]);

        if (!$result) {
            return($stmt->errorInfo());
        }

        return TRUE;
    }

}
