<?php

namespace Models;

class CategoriesModel {

    public function __construct() {
        
    }

    /**
     * Получает таблицу категорий вопросов
     */
    public function getCategories() {
        $pdo = \App\Config::pdo();
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

    /**
     * Добавляет категорию
     */
    public function addTopic($name) {
        $pdo = \App\Config::pdo();
        $sql = "INSERT INTO categories (name) VALUES (?)";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$name]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    /**
     * Удаляет категорию по Id
     */
    public function deleteTopic($id) {
        $pdo = \App\Config::pdo();
        $sql = "DELETE FROM categories WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    /**
     * Изменяет категорию по Id
     */
    public function changeTopic($id, $name) {
        $pdo = \App\Config::pdo();
        $sql = "UPDATE categories SET name = ? WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$name, $id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    /**
     * Устанавливает категорию по Id - текущей в настройках сеанса
     */
    function setCurrentCategory($Category_id) {
        setcookie('category', $Category_id, time() + 3600);
    }

    /**
     * Получает текущую категорию из настроек сеанса
     */
    function getCurrentCategoryFromCookies() {

        if (isset($_COOKIE['category'])) {
            return $_COOKIE['category'];
        } else {
            return 1;
        }
    }

    /**
     * Получает Id категории по имени
     */
    function getCategoryByName($name) {
        $pdo = \App\Config::pdo();
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
