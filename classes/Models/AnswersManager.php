<?php

namespace Models;

class AnswersManager 
{

    public function __construct() 
    {
    }

    public function addAnswer($question_id, $answer) 
    {
        $adminsManager = new \Models\AdminsManager();
        $user = $adminsManager->getAdminFromGlobals();
        $user_id = $adminsManager->getAdminByLogin($user);

        $pdo = \App\Db::pdo();
        $sql = "INSERT INTO answers (question_id, user_id, answer) VALUES (?,?,?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$question_id, $user_id['id'], $answer]);

        if (!$result) {
            return $stmt->errorInfo();
        }
        return $result;
    }

    public function changeAnswer($answer_id, $answer) 
    {
        $adminsManager = new \Models\AdminsManager();
        $user = $adminsManager->getAdminFromGlobals();
        $user_id = $adminsManager->getAdminByLogin($user);

        $pdo = \App\Db::pdo();
        $sql = "UPDATE answers SET answer = ?, user_id = ? where id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$answer, $user_id['id'], $answer_id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $result;
    }

    public function getAnswers() 
    {
        $pdo = \App\Db::pdo();
        $sql = "SELECT answers.id, answers.question_id, answers.answer, answers.user_id,
                admins.login
                FROM answers
                LEFT JOIN admins on admins.id = answers.user_id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }

    public function getAnswersOnQuestions($questionId) 
    {
        $pdo = \App\Db::pdo();
        $sql = "SELECT answers.id, answers.question_id, answers.answer, answers.user_id,
                admins.login
                FROM answers
                LEFT JOIN admins ON admins.id = answers.user_id
                WHERE question_id = ?";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$questionId]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }
}
