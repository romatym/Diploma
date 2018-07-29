<?php

namespace Models;

class AdminsManager 
{
    
    public function __construct() 
    {
    }
    
    public function addAdmin($login, $password) 
    {
        $pdo = \App\Db::pdo();
        $sql = "INSERT INTO admins (login, password) VALUES (?,?)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$login, $password]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }
    
    public function setPassword($login, $password) 
    {
        $pdo = \App\Db::pdo();
        $sql = "UPDATE admins SET password = ? WHERE login = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$password, $login]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }
    
    public function deleteAdmin($id) 
    {
        $pdo = \App\Db::pdo();
        $sql = "DELETE FROM admins WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }
    
    public function getAdminsList() 
    {
        $pdo = \App\Db::pdo();
        $sql = "SELECT login, id FROM admins";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }

    function getAdminByLogin($login) 
    {
        $pdo = \App\Db::pdo();
        $sql = "SELECT id, login, password FROM admins WHERE login = ?";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$login]);

        if (!$result) {
            return($stmt->errorInfo());
        }
        $table = $stmt->fetchAll();
        foreach ($table as $key => $value) {
            return $value;
        }
    }

    function getAdminFromGlobals() 
    {
        if (isset($_SESSION['admin'])) {
            return $_SESSION['admin'];
        } elseif (isset($_COOKIE['admin'])) {
            return $_COOKIE['admin'];
        } else {
            return '';
        }
    }

    function putAdminToGlobals($admin) 
    {
        setcookie('admin', $admin, time() + 3600);
        $_SESSION['admin'] = $admin;
    }

} 
