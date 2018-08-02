<?php

namespace Models;

class AnswersManager 
{

    public function __construct() 
    {
    }

/**
 * Добавляет ответ
 */    
    public function addAnswer($question_id, $answer) 
    {
        $adminsManager = new \Models\AdminsManager();
        $user = $adminsManager->getAdminFromGlobals();
        $admin_id = $adminsManager->getAdminByLogin($user);

        $pdo = \App\Db::pdo();
        $sql = "INSERT INTO answers (question_id, admin_id, answer) VALUES (?,?,?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$question_id, $admin_id['id'], $answer]);

        if (!$result) {
            return $stmt->errorInfo();
        }
        return $result;
    }

/**
 * Изменяет ответ по Id
 */    
    public function changeAnswer($answer_id, $answer) 
    {
        $adminsManager = new \Models\AdminsManager();
        $user = $adminsManager->getAdminFromGlobals();
        $admin_id = $adminsManager->getAdminByLogin($user);

        $pdo = \App\Db::pdo();
        $sql = "UPDATE answers SET answer = ?, admin_id = ? where id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$answer, $admin_id['id'], $answer_id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $result;
    }

/**
 * Полчает таблицу ответов
 */    
    public function getAnswers() 
    {
        $pdo = \App\Db::pdo();
        $sql = "SELECT answers.id, answers.question_id, answers.answer, answers.admin_id,
                admins.login
                FROM answers
                LEFT JOIN admins on admins.id = answers.admin_id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }

/**
 * Полчает таблицу ответов на выбранный вопрос по Id вопроса
 */    
    
    public function getAnswersOnQuestions($questionId) 
    {
        $pdo = \App\Db::pdo();
        $sql = "SELECT answers.id, answers.question_id, answers.answer, answers.admin_id,
                admins.login
                FROM answers
                LEFT JOIN admins ON admins.id = answers.admin_id
                WHERE question_id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$questionId]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }
}
