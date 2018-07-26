<?php

namespace Controllers;

use Models;

class Diploma extends \App\Controller
{
    
    public function index ($params) 
    {
        $Db = new \App\Db(); 
        $pdo = $Db->pdo;

        $adminsManager = new \Models\AdminsManager();
        $admin = $adminsManager->getAdminFromGlobals();
        $hidden = FALSE;
        if($admin=='') {
            $hidden = TRUE;
        }
        $questions = new \Models\QuestionsManager();
        $listQuestions = $questions->getQuestions($pdo, $hidden);

        $categories = new \Models\CategoriesManager();
        $listCategories = $categories->getCategories($pdo);
        $selectedCategory = $categories->getCurrentCategoryFromCookies();

        $answers = new \Models\AnswersManager();
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

    public function login ($params) 
    {
        return $this->render('login.tmpl', $params);
    }

    public function sign_in ($params) 
    {
        if ($this->Checklogin()) {
           
            $errors = [];
        
           //************* начало сессии *******************
            session_start();
            //************* начало сессии *******************

            $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($this->loginAsAdmin($errors, $login, $password)) {
                $this->redirectToHomepage();
            } else {
                print_r($errors);
            }
        }
    }
    
    function loginAsAdmin(&$errors, $login, $password) 
    {
        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $adminManager = new \Models\AdminsManager();
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
    
    function checklogin() 
    {
        if (!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['sign_in'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function logout ($params) 
    {
        setcookie('admin', "", time() - 3600);
        setcookie('PHPSESSID', "", time() - 3600);
        $_SESSION = [];
        
        $this->redirectToHomepage();
    }
    
    public function accounts ($params) 
    {
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $adminsManager = new \Models\AdminsManager();
        $adminsList = $adminsManager->getAdminsList($pdo);
        $admin = $adminsManager->getAdminFromGlobals();
        
        $categoriesManager = new \Models\CategoriesManager();
        $listCategories = $categoriesManager->getCategories($pdo);
        
        return $this->render('accounts.tmpl', array(
            'admins' => $adminsList,
            'topics' => $listCategories,
            'admin' => $admin
            )
        );
    }

    function addAdmin() 
    {
        $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        $adminsManager = new \Models\AdminsManager();
        $adminsManager->addAdmin($login, $password);
        
        $this->redirectToHomepage('index.php?action=accounts');
    }
    
    function addTopic() 
    {
        $name = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);

        $categoriesManager = new \Models\CategoriesManager();
        $categoriesManager->addTopic($name);
        
        $this->redirectToHomepage('index.php?action=accounts');
    }
    
    function deleteTopic() 
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        $categoriesManager = new \Models\CategoriesManager();
        $categoriesManager->deleteTopic($id);
        
        $this->redirectToHomepage('index.php?action=accounts');
    }
    
    function deleteAdmin() 
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        $adminsManager = new \Models\AdminsManager();
        $adminsManager->deleteAdmin($id);
        
        $this->redirectToHomepage('index.php?action=accounts');
    }
    
    function actionAccounts() 
    {
        if (isset($_POST['addAdmin'])) {

            $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
            $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

            $adminsManager = new \Models\AdminsManager();
            $adminsManager->addAdmin($login, $password);
        }
        elseif (isset($_POST['addTopic'])) {

            $name = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);

            $categoriesManager = new \Models\CategoriesManager();
            $categoriesManager->addTopic($name);
        } elseif (isset($_GET['action'])) {

            $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

            if ($_GET['action'] == 'deleteTopic') {
                $categoriesManager = new \Models\CategoriesManager();
                $categoriesManager->deleteTopic($id);
            } elseif ($_GET['action'] == 'deleteAdmin') {
                $adminsManager = new \Models\AdminsManager();
                $adminsManager->deleteAdmin($id);
            }
        }
        
        $this->redirectToHomepage('index.php?action=accounts');    
    }
    
    public function answer ($params) 
    {
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerText = '';
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $questionsManager = new \Models\QuestionsManager();
        $question = $questionsManager->getQuestionById($pdo, $questionId);

        return $this->render('answer.tmpl', array(
            'question' => $question,
            'answerText'=>$answerText
            )
        );
    }
     
    public function answerModify ($params) 
    {
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerId = filter_input(INPUT_GET, 'answerId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerText = filter_input(INPUT_GET, 'answerText', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $questionsManager = new \Models\QuestionsManager();
        $question = $questionsManager->getQuestionById($pdo, $questionId);

        return $this->render('answer.tmpl', array(
            'question' => $question,
            'answerId' => $answerId,
            'answerText'=>$answerText
            )
        );
    }
    
    public function sendAnswer ($params) 
    {
        $questionId = filter_input(INPUT_POST, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_SPECIAL_CHARS);

        $answersManager = new \Models\AnswersManager();
        $newAnswer = $answersManager->addAnswer($questionId, $answer);

        $this->redirectToHomepage();
    }
    
    public function changeAnswer ($params) 
    {
        $questionId = filter_input(INPUT_POST, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerId = filter_input(INPUT_POST, 'answerId', FILTER_SANITIZE_SPECIAL_CHARS);

        $answersManager = new \Models\AnswersManager();
        $newAnswer = $answersManager->changeAnswer($answerId, $answer);

        $this->redirectToHomepage();
              
    }
    
    public function showAnswers ($params) 
    {
        $questionId = $this->GetQuestionId();
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $questionsManager = new \Models\QuestionsManager();
        $question = $questionsManager->getQuestionById($pdo, $questionId);
        
        $answers = new \Models\AnswersManager();
        $listAnswers = $answers->getAnswersOnQuestions($pdo, $questionId);

        $adminManager = new \Models\AdminsManager();
        $admin = $adminManager->getAdminFromGlobals();
        
        return $this->render('answers.tmpl', array(
            'question' => $question,
            'answers' => $listAnswers,
            'admin' => $admin
            )
        );
    }
                
    function getQuestionId() 
    {
        if (isset($_GET['questionId'])) {
            $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
            return $questionId;
        }
        return '';
    }
    
    function getAnswerText() 
    {
        if (isset($_GET['answerText'])) {
            $answerText = filter_input(INPUT_GET, 'answerText', FILTER_SANITIZE_SPECIAL_CHARS);
            return $answerText;
        }

        return '';
    }
    
    public function hideQuestion() 
    {
        $id = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        if(!empty($id)) {
            $questionManager = new \Models\QuestionsManager();
            $questionManager->changeHidden($id, TRUE);
        }
        $this->redirectToHomepage();
    }
       
    public function publishQuestion() 
    {
        $id = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        if(!empty($id)) {
            $questionManager = new \Models\QuestionsManager();
            $questionManager->changeHidden($id, FALSE);
        }
        $this->redirectToHomepage();
    }
    
    public function deleteQuestion() 
    {
        $id = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        if(!empty($id)) {
            $questionManager = new \Models\QuestionsManager();
            $questionManager->deleteQuestion($id);
        }
        $this->redirectToHomepage();
    }
    
    public function askQuestion() 
    {
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $categoriesManager = new \Models\CategoriesManager();
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
    
    public function questionModify() 
    {
        $Db = new \App\Db();
        $pdo = $Db->pdo;

        $categoriesManager = new \Models\CategoriesManager();
        $listCategories = $categoriesManager->getCategories($pdo);
        
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $questionsManager = new \Models\QuestionsManager();
        $questionParameters = $questionsManager->getQuestionById($pdo, $questionId);
        
        return $this->render('question.tmpl', array(
            'categories' => $listCategories,
            'question' => $questionParameters
            )
        );
                
    }
    
    public function sendQuestion() 
    {
        if (isset($_POST['sendQuestion'])) {

            $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
            $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
            $topic = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);
            $question = filter_input(INPUT_POST, 'question', FILTER_SANITIZE_SPECIAL_CHARS);

            $questionsManager = new \Models\QuestionsManager();
            $newQuestion = $questionsManager->addQuestion($category, $name, $email, $topic, $question);

        }
        
        $this->redirectToHomepage();
    }
    
    public function changeQuestion() 
    {
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
        $topic = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);
        $question = filter_input(INPUT_POST, 'question', FILTER_SANITIZE_SPECIAL_CHARS);
        $questionId = filter_input(INPUT_POST, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);

        $questionsManager = new \Models\QuestionsManager();
        $newQuestion = $questionsManager->changeQuestion($questionId, $topic, $question);

        $this->redirectToHomepage();
    }
    
    function home() 
    {
        
        $this->redirectToHomepage();
    }
    
    public function redirectToHomepage($extra = '') 
    {
        $host  = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

        header("Location: http://$host$uri/$extra");
        exit;
    }
    
}