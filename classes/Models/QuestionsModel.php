<?php

namespace Models;

class QuestionsModel {

    public function __construct() {
        
    }

    /**
     * Добавляет вопрос
     */
    public function addQuestion($category, $name, $email, $topic, $question) {

        $pdo = \App\Config::pdo();

        $categoriesManager = new CategoriesModel();
        $categoryId = $categoriesManager->getCategoryByName($category);

        $sql = "INSERT INTO questions (category_id, author, email, topic, question, date) VALUES (?,?,?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$categoryId, $name, $email, $topic, $question, date("Y-m-d H:i:s")]);

        if (!$result) {
            return $stmt->errorInfo();
        }
        return $result;
    }

    /**
     * Получает все скрытые / любые вопросы и связанные с ними данные для вывода на главной странице
     */
    public function getQuestions($hidden) {
        $pdo = \App\Config::pdo();
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

    /**
     * Получает все вопросы без ответов
     */
    public function getQuestionsWithoutAnswers() {
        $pdo = \App\Config::pdo();
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

    /**
     * Формирует дерево вопросов-ответов для вывода на главной странице
     */
    public function getTreeOfQuestionsAndAnswers($Categories, $Questions, $Answers) {
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

    /**
     * Получает данные вопроса по Id
     */
    function getQuestionById($questionId) {
        $pdo = \App\Config::pdo();
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

    /**
     * Удаляет вопрос по Id
     */
    public function deleteQuestion($id) {
        $pdo = \App\Config::pdo();
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

    /**
     * Меняет признак hidden у вопроса
     */
    public function changeHidden($id, $hiddenNewValue) {
        $pdo = \App\Config::pdo();
        $sql = "UPDATE questions SET hidden = ? WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$hiddenNewValue, $id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        return TRUE;
    }

    /**
     * Меняет вопрос по Id
     */
    public function changeQuestion($id, $topic, $text) {
        $pdo = \App\Config::pdo();
        $sql = "UPDATE questions SET topic = ?, question = ? WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$topic, $text, $id]);

        if (!$result) {
            return($stmt->errorInfo());
        }

        return TRUE;
    }

}
