<?php

namespace Forum;

class AnswersManager 
{

    public function __construct() 
    {
    }

    public function addAnswer($question_id, $answer) 
    {

        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $adminsManager = new \Forum\AdminsManager();
        $user = $adminsManager->getAdminFromGlobals();
        $user_id = $adminsManager->getAdminByLogin($pdo, $user);

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

        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $adminsManager = new \Forum\AdminsManager();
        $user = $adminsManager->getAdminFromGlobals();
        $user_id = $adminsManager->getAdminByLogin($pdo, $user);

        $sql = "UPDATE answers SET answer = ?, user_id = ? where id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$answer, $user_id['id'], $answer_id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $result;
    }

    public function getAnswers($pdo) 
    {

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

    public function getAnswersOnQuestions($pdo, $questionId) 
    {

        $sql = "SELECT answers.id, answers.question_id, answers.answer, answers.user_id,
                    admins.login
                FROM answers
                LEFT JOIN admins on admins.id = answers.user_id
                WHERE question_id = " . $questionId;
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }

}
