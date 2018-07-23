<?php

namespace Forum;

class AdminsManager {
    
    public function __construct() {
    }
    
    public function addAdmin($login, $password) {
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $sql = "INSERT INTO admins (login, password) VALUES (?,?)";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$login, $password]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }
    
    public function setPassword($login, $password) {
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $sql = "UPDATE admins SET password = ? where login = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$password, $login]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }
    
    public function deleteAdmin($id) {
        
        $Db = new \App\Db();
        $pdo = $Db->pdo;
        
        $sql = "delete from admins where id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }
    
    public function getAdminsList($pdo) {

        $sql = "SELECT login, id FROM admins";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }

    function getAdminByLogin($pdo, $login) {

        $sql = "SELECT id, login, password FROM admins where login = '" . $login . "'";
        $table = $pdo->query($sql);
        foreach ($table as $row) {
            return $row;
        }

    }

    function GetAdminFromGlobals() {
 
    if (isset($_SESSION['admin'])) {
        return $_SESSION['admin'];
    }
    elseif (isset($_COOKIE['admin'])) {
        return $_COOKIE['admin'];
    }
    else {
        return '';
    }
        
}

function PutAdminToGlobals($admin) {
    
    setcookie('admin', $admin, time() + 3600);
    $_SESSION['admin'] = $admin;
    
}

} 
