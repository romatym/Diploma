<?php

namespace Controllers;

use Models;

class Diploma extends \App\Controller
{
 /**
 * Отображает страницу index
 */
    public function index () 
    {
        $adminsManager = new \Models\AdminsManager();
        $admin = $adminsManager->getAdminFromGlobals();
        $hidden = FALSE;
        if($admin=='') {
            $hidden = TRUE;
        }
        $questions = new \Models\QuestionsManager();
        $listQuestions = $questions->getQuestions($hidden);

        $categories = new \Models\CategoriesManager();
        $listCategories = $categories->getCategories();
        $selectedCategory = $categories->getCurrentCategoryFromCookies();

        $answers = new \Models\AnswersManager();
        $listAnswers = $answers->getAnswers();

        $treeOfQuestions = $questions->getTreeOfQuestionsAndAnswers(
                    $listCategories, $listQuestions, $listAnswers);
        
        return $this->render('index.tmpl', array(
            'treeOfQuestions' => $treeOfQuestions,
            'categories' => $listCategories,
            'selectedCategory' => $selectedCategory,
            'admin' => $admin)
        );
    }

/**
 * Отображает страницу login
 * @params параметры вывода шаблона
 */
    public function login ($params) 
    {
        return $this->render('login.tmpl', $params);
    }

/**
 * Сверяет введенные логин/пароль с данными БД
 * $login, $password - введенные пользователем данные
 * $errors - лог ошибок
 */
    function loginAsAdmin(&$errors, $login, $password) 
    {
        $adminManager = new \Models\AdminsManager();
        $adminParameters = $adminManager->getAdminByLogin($login);

        if ($adminParameters['login'] == $login && $adminParameters['password'] == $password) {
            $_SESSION['admin'] = $adminParameters['login'];
            setcookie('admin', $adminParameters['login'], time() + 3600);
            return TRUE;
        } else {
            $errors[] = 'Пользователь не авторизован';
            return FALSE;
        }
    }
    
/**
 * Проверяет, авторизован ли админ
 */    
    function checklogin() 
    {
        if (!empty($_POST['login']) && !empty($_POST['password']) && !empty($_POST['sign_in'])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

/**
 * разлогинивает администратора
 */    
    public function logout () 
    {
        setcookie('admin', "", time() - 3600);
        setcookie('PHPSESSID', "", time() - 3600);
        $_SESSION = [];
        
        $this->redirectToHomepage();
    }

/**
 * запускает сессию и сохраняет параметры авторизации
 */ 
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
    
/**
 * Отображает страницу accounts
 */    
    public function accounts () 
    {
        $adminsManager = new \Models\AdminsManager();
        $adminsList = $adminsManager->getAdminsList();
        $admin = $adminsManager->getAdminFromGlobals();
        
        $login = filter_input(INPUT_GET, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $categoriesManager = new \Models\CategoriesManager();
        $listCategories = $categoriesManager->getCategories();
        
        $topic = filter_input(INPUT_GET, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);
        $topicId = NULL;
        if(isset($topic)) {
            $topicId = $categoriesManager->getCategoryByName($topic);
        }
        
        $questions = new \Models\QuestionsManager();
        $listQuestions = $questions->getQuestionsWithoutAnswers();
        
        return $this->render('accounts.tmpl', array(
            'admins' => $adminsList,
            'topics' => $listCategories,
            'questions' => $listQuestions,
            'admin' => $admin,
            'login' => $login,
            'topic' => $topic, 
            'topicId' => $topicId
            )
        );
    }

/**
 * Добавляет администратора
 */
    function addAdmin() 
    {
        $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);

        $adminsManager = new \Models\AdminsManager();
        $adminsManager->addAdmin($login, $password);
        
        $this->redirectToHomepage('index.php?action=accounts');
    }

/**
 * Добавляет категорию
 */    
    function addTopic() 
    {
        $name = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);

        $categoriesManager = new \Models\CategoriesManager();
        $categoriesManager->addTopic($name);
        
        $this->redirectToHomepage('index.php?action=accounts');
    }

/**
 * Открывает админзону для изменения категории
 */    
    function modifyTopic() 
    {
        $name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        $this->redirectToHomepage('?action=accounts&topic='.$name);
    }
    
/**
 * Изменяет категорию
 */    
    function changeTopic() 
    {
        $name = filter_input(INPUT_POST, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);
        $id = filter_input(INPUT_POST, 'topicId', FILTER_SANITIZE_SPECIAL_CHARS);

        $categoriesManager = new \Models\CategoriesManager();
        $categoriesManager->changeTopic($id, $name);
        
        $this->redirectToHomepage('index.php?action=accounts');
    }
    
    
/**
 * удаляет категорию
 */    
    function deleteTopic() 
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        $categoriesManager = new \Models\CategoriesManager();
        $categoriesManager->deleteTopic($id);
        
        $this->redirectToHomepage('index.php?action=accounts');
    }

/**
 * удаляет администратора
 */    
    function deleteAdmin() 
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);

        $adminsManager = new \Models\AdminsManager();
        $adminsManager->deleteAdmin($id);
        
        $this->redirectToHomepage('index.php?action=accounts');
    }

/**
 * обрабатывает действие на странице админзоны при нажатии ссылки
 */    
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

/**
 * открывает страницу answer для ввода ответа на вопрос
 */    
    public function answer () 
    {
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerText = '';
        
        $questionsManager = new \Models\QuestionsManager();
        $question = $questionsManager->getQuestionById($questionId);

        return $this->render('answer.tmpl', array(
            'question' => $question,
            'answerText'=>$answerText
            )
        );
    }

/**
 * открывает страницу answer для редактирования ответа на вопрос
 */    
    public function answerModify () 
    {
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerId = filter_input(INPUT_GET, 'answerId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerText = filter_input(INPUT_GET, 'answerText', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $questionsManager = new \Models\QuestionsManager();
        $question = $questionsManager->getQuestionById($questionId);

        return $this->render('answer.tmpl', array(
            'question' => $question,
            'answerId' => $answerId,
            'answerText'=>$answerText
            )
        );
    }
    
/**
 * сохраняет введенный ответ на вопрос
 */    
    public function sendAnswer () 
    {
        $questionId = filter_input(INPUT_POST, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_SPECIAL_CHARS);

        $answersManager = new \Models\AnswersManager();
        $newAnswer = $answersManager->addAnswer($questionId, $answer);

        $this->redirectToHomepage();
    }

/**
 * сохраняет измененный ответ на вопрос
 */    
    public function changeAnswer () 
    {
        $questionId = filter_input(INPUT_POST, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_SPECIAL_CHARS);
        $answerId = filter_input(INPUT_POST, 'answerId', FILTER_SANITIZE_SPECIAL_CHARS);

        $answersManager = new \Models\AnswersManager();
        $newAnswer = $answersManager->changeAnswer($answerId, $answer);

        $this->redirectToHomepage();
              
    }

/**
 * Отображает страницу Answers
 */    
    public function showAnswers () 
    {
        $questionId = $this->GetQuestionId();
        
        $questionsManager = new \Models\QuestionsManager();
        $question = $questionsManager->getQuestionById($questionId);
        
        $answers = new \Models\AnswersManager();
        $listAnswers = $answers->getAnswersOnQuestions($questionId);

        $adminManager = new \Models\AdminsManager();
        $admin = $adminManager->getAdminFromGlobals();
        
        return $this->render('answers.tmpl', array(
            'question' => $question,
            'answers' => $listAnswers,
            'admin' => $admin
            )
        );
    }

/**
 * получает Id вопроса 
 */    
    function getQuestionId() 
    {
        if (isset($_GET['questionId'])) {
            $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
            return $questionId;
        }
        return '';
    }
    
/**
 * скрывает вопрос на главной странице
 */    
    public function hideQuestion() 
    {
        $id = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        if(!empty($id)) {
            $questionManager = new \Models\QuestionsManager();
            $questionManager->changeHidden($id, TRUE);
        }
        $this->redirectToHomepage();
    }

/**
 * публикует вопрос на главной странице
 */    
    public function publishQuestion() 
    {
        $id = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        if(!empty($id)) {
            $questionManager = new \Models\QuestionsManager();
            $questionManager->changeHidden($id, FALSE);
        }
        $this->redirectToHomepage();
    }
   
/**
 * удаляет вопрос по Id
 */    
    public function deleteQuestion() 
    {
        $id = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $returnPage = filter_input(INPUT_GET, 'returnPage', FILTER_SANITIZE_SPECIAL_CHARS);
        
        if(!empty($id)) {
            $questionManager = new \Models\QuestionsManager();
            $questionManager->deleteQuestion($id);
        }
        
        if($returnPage=='accounts') {
            $this->aminZone();
        }
        else {
            $this->redirectToHomepage();
        }
    }

/**
 * Отображает страницу ввода вопроса question
 */    
    public function askQuestion() 
    {
        $categoriesManager = new \Models\CategoriesManager();
        $listCategories = $categoriesManager->getCategories();
        
        if (isset($_GET['category'])) {
            $currentCategory = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
        } else {
            $currentCategory = 1;
        }
        
        return $this->render('question.tmpl', array(
            'categories' => $listCategories,
            'currentCategory' => $currentCategory
            )
        );
    }
 
/**
 * Отображает страницу редактирования вопроса question
 */    
    public function questionModify() 
    {
        $categoriesManager = new \Models\CategoriesManager();
        $listCategories = $categoriesManager->getCategories();
        
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);
        $returnPage = filter_input(INPUT_GET, 'returnPage', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $questionsManager = new \Models\QuestionsManager();
        $questionParameters = $questionsManager->getQuestionById($questionId);
        
        return $this->render('question.tmpl', array(
            'categories' => $listCategories,
            'question' => $questionParameters,
            'returnPage' => $returnPage
            )
        );
        
    }
    
/**
 * Сохраняет введенный вопрос
 */    
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
    
/**
 * Сохраняет измененный вопрос
 */    
    public function changeQuestion() 
    {
        $category = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
        $name = filter_input(INPUT_GET, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_GET, 'email', FILTER_SANITIZE_SPECIAL_CHARS);
        $topic = filter_input(INPUT_GET, 'topic', FILTER_SANITIZE_SPECIAL_CHARS);
        $question = filter_input(INPUT_GET, 'question', FILTER_SANITIZE_SPECIAL_CHARS);
        $questionId = filter_input(INPUT_GET, 'questionId', FILTER_SANITIZE_SPECIAL_CHARS);

        $returnPage = filter_input(INPUT_GET, 'returnPage', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $questionsManager = new \Models\QuestionsManager();
        $newQuestion = $questionsManager->changeQuestion($questionId, $topic, $question);

        if($returnPage=='accounts') {
            $this->aminZone();
        }
        else {
            $this->redirectToHomepage();
        }
        
    }

/**
 * Открывает страницу для редактирования пароля
 */    
    public function changePassword() 
    {
        $login = filter_input(INPUT_GET, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
        $this->redirectToHomepage('?action=accounts&login='.$login);    
    }

/**
 * Сохраняет измененный пароль
 */    
    public function changeAdmin() 
    {
        $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_SPECIAL_CHARS);
        
        $adminManager = new \Models\AdminsManager();
        $adminManager->setPassword($login, $password);
        
        $this->redirectToHomepage('?action=accounts');    
    }

/**
 * Перенаправляет на главную страницу
 */    
    function home() 
    {
        $this->redirectToHomepage();
    }

/**
 * Перенаправляет на страницу админзоны
 */    
    function aminZone() 
    {
        $this->redirectToHomepage('?action=accounts');
    }

/**
 * Перенаправляет на указанную страницу
 */    
    public function redirectToHomepage($extra = '') 
    {
        $host  = $_SERVER['HTTP_HOST'];
        $uri = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

        header("Location: http://$host$uri/$extra");
        exit;
    }
    
}