<?php

namespace Controllers;

use Forum;

class Diploma extends \App\Controller
{
    
    public function index ($params) {
        
        $Db = new \App\Db(); 
        $pdo = $Db->pdo;

        $adminsManager = new \Forum\AdminsManager();
        $admin = $adminsManager->GetAdminFromGlobals();
        $hidden = FALSE;
        if($admin=='') {
            $hidden = TRUE;
        }
        $questions = new \Forum\QuestionsManager();
        $listQuestions = $questions->getQuestions($pdo, $hidden);

        $categories = new \Forum\CategoriesManager();
        $listCategories = $categories->getCategories($pdo);
        $selectedCategory = $categories->GetCurrentCategoryFromCookies();

        $answers = new \Forum\AnswersManager();
        $listAnswers = $answers->getAnswers($pdo);

        $treeOfQuestions = $questions->getTreeOfQuestionsWithAnswers(
                    $listCategories, $listQuestions, $listAnswers);
        
        return $this->render('index.tmpl', array(
            'treeOfQuestions' => $treeOfQuestions,
            'categories' => $listCategories,
            'selectedCategory' => $selectedCategory,
            'admin' => $admin)
        );
        
    }

    public function login ($params) {
        
        return $this->render('login.tmpl', $params);
        
    }

    public function sign_in ($params) {
        
        if ($this->Checklogin()) {
           
            $errors = [];
        
           //************* начало сессии *******************
            session_start();
            //************* начало сессии *******************

            $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($this->loginAsAdmin($errors, $login, $password)) {
                $this->RedirectToHomepage();
            } else {
                print_r($errors);
            }
        }
        
    }
    
    function loginAsAdmin(&$errors, $login, $password) {

        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $adminManager = new \Forum\AdminsManager();
        $adminParameters = $adminManager->getAdminByLogin($pdo, $login);

        if ($adminParameters['login'] == $login && $adminParameters['password'] == $password) {
            $_SESSION['admin'] = $adminParameters['login'];
            setcookie('admin', $adminParameters['login'], time() + 3600);
            return TRUE;
        } else {
            $errors[] = 'Пользователь не авторизован';
            return FALSE;
        }
    }
    
    function Checklogin() {

        if (!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['sign_in'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function logout ($params) {
        
        setcookie('admin', "", time() - 3600);
        setcookie('PHPSESSID', "", time() - 3600);
        $_SESSION = [];
        
        $this->RedirectToHomepage();
        
    }
    
    public function accounts ($params) {
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $adminsManager = new \Forum\AdminsManager();
        $adminsList = $adminsManager->getAdminsList($pdo);
        $admin = $adminsManager->GetAdminFromGlobals();
        
        $categoriesManager = new \Forum\CategoriesManager();
        $listCategories = $categoriesManager->getCategories($pdo);
        
        return $this->render('accounts.tmpl', array(
            'admins' => $adminsList,
            'topics' => $listCategories,
            'admin' => $admin
            )
        );
        
    }
    
    function ActionAccounts() {

        if (isset($_POST['addAdmin'])) {

            $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

            $adminsManager = new \Forum\AdminsManager();
            $adminsManager->addAdmin($login, $password);
        }
        elseif (isset($_POST['addTopic'])) {

            $name = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);

            $categoriesManager = new \Forum\CategoriesManager();
            $categoriesManager->addTopic($name);
        } elseif (isset($_GET['action'])) {

            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($_GET['action'] == 'deleteTopic') {
                $categoriesManager = new \Forum\CategoriesManager();
                $categoriesManager->deleteTopic($id);
            } elseif ($_GET['action'] == 'deleteAdmin') {
                $adminsManager = new \Forum\AdminsManager();
                $adminsManager->deleteAdmin($id);
            }
        }
        
        $this->RedirectToHomepage('/accounts');    
        
    }
    
    public function answer ($params) {
        
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerText = '';
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $questionsManager = new \Forum\QuestionsManager();
        $question = $questionsManager->getQuestionById($pdo, $questionId);

        return $this->render('answer.tmpl', array(
            'question' => $question,
            'answerText'=>$answerText
            )
        );
              
    }
     
    public function answerModify ($params) {
        
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerId = filter_input(INPUT_GET, 'answerId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerText = filter_input(INPUT_GET, 'answerText', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $questionsManager = new \Forum\QuestionsManager();
        $question = $questionsManager->getQuestionById($pdo, $questionId);

        return $this->render('answer.tmpl', array(
            'question' => $question,
            'answerId' => $answerId,
            'answerText'=>$answerText
            )
        );
              
    }
    
    public function sendAnswer ($params) {
        
        $questionId = filter_input(INPUT_POST, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_SPECIAL_CHARS);

        $answersManager = new \Forum\AnswersManager();
        $newAnswer = $answersManager->addAnswer($questionId, $answer);

        $this->RedirectToHomepage();
              
    }
    
    public function changeAnswer ($params) {
        
        $questionId = filter_input(INPUT_POST, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerId = filter_input(INPUT_POST, 'answerId', FILTER_SANITIZE_SPECIAL_CHARS);

        $answersManager = new \Forum\AnswersManager();
        $newAnswer = $answersManager->changeAnswer($answerId, $answer);

        $this->RedirectToHomepage();
              
    }
    
    public function showAnswers ($params) {
        
        $questionId = $this->GetQuestionId();
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $questionsManager = new \Forum\QuestionsManager();
        $question = $questionsManager->getQuestionById($pdo, $questionId);
        
        $answers = new \Forum\AnswersManager();
        $listAnswers = $answers->getAnswersOnQuestions($pdo, $questionId);

        $adminManager = new \Forum\AdminsManager();
        $admin = $adminManager->GetAdminFromGlobals();
        
        return $this->render('answers.tmpl', array(
            'question' => $question,
            'answers' => $listAnswers,
            'admin' => $admin
            )
        );
        
    }
                
    function GetQuestionId() {

        if (isset($_GET['questionId'])) {
            $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
            return $questionId;
        }

        return '';
    }
    
    function GetAnswerText() {

        if (isset($_GET['answerText'])) {
            $answerText = filter_input(INPUT_GET, 'answerText', FILTER_SANITIZE_SPECIAL_CHARS);
            return $answerText;
        }

        return '';
    }
    
    public function hideQuestion() {
        
        $id = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        if(!empty($id)) {
            $questionManager = new \Forum\QuestionsManager();
            $questionManager->changeHidden($id, TRUE);
        }
        $this->RedirectToHomepage();
    }
       
    public function publishQuestion() {
        
        $id = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        if(!empty($id)) {
            $questionManager = new \Forum\QuestionsManager();
            $questionManager->changeHidden($id, FALSE);
        }
        $this->RedirectToHomepage();
    }
    
    public function deleteQuestion() {
        
        $id = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        if(!empty($id)) {
            $questionManager = new \Forum\QuestionsManager();
            $questionManager->deleteQuestion($id);
        }
        $this->RedirectToHomepage();
    }
    
    public function askQuestion() {
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $categoriesManager = new \Forum\CategoriesManager();
        $listCategories = $categoriesManager->getCategories($pdo);
        
        if (isset($_GET['category'])) {
            $currentCategory = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
        } else {
            $currentCategory = 1;
        }
        
        return $this->render('question.tmpl', array(
            'categories' => $listCategories,
            'category' => $currentCategory
            )
        );
                
    }
    
    public function questionModify() {
    
        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $categoriesManager = new \Forum\CategoriesManager();
        $listCategories = $categoriesManager->getCategories($pdo);
        
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $questionsManager = new \Forum\QuestionsManager();
        $questionParameters = $questionsManager->getQuestionById($pdo, $questionId);
        
        return $this->render('question.tmpl', array(
            'categories' => $listCategories,
            'question' => $questionParameters
            )
        );
                
    }
    
    public function sendQuestion() {
        
        if (isset($_POST['sendQuestion'])) {

            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
            $topic = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);
            $question = filter_input(INPUT_POST, 'question', FILTER_SANITIZE_SPECIAL_CHARS);

            $questionsManager = new \Forum\QuestionsManager();
            $newQuestion = $questionsManager->addQuestion($category, $name, $email, $topic, $question);

        }
        
        $this->RedirectToHomepage();
    }
    
    public function changeQuestion() {
        
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
        $topic = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);
        $question = filter_input(INPUT_POST, 'question', FILTER_SANITIZE_SPECIAL_CHARS);
        $questionId = filter_input(INPUT_POST, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);

        $questionsManager = new \Forum\QuestionsManager();
        $newQuestion = $questionsManager->changeQuestion($questionId, $topic, $question);

        $this->RedirectToHomepage();
    }
    
    function Home() {
        
        $this->RedirectToHomepage();
    }
    
    public function RedirectToHomepage($extra = '') {
        
        $host  = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

        header("Location: http://$host$uri$extra");
        exit;
    }
    
}