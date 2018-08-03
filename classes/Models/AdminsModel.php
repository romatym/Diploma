<?php

namespace Models;

class AdminsModel {

    public function __construct() {
        
    }

    /**
     * Добавляет администоратора
     */
    public function addAdmin($login, $password) {
        $pdo = \App\Config::pdo();
        $sql = "INSERT INTO admins (login, password) VALUES (?,?)";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$login, $password]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    /**
     * Изменяет пароль
     */
    public function setPassword($login, $password) {
        $pdo = \App\Config::pdo();
        $sql = "UPDATE admins SET password = ? WHERE login = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$password, $login]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    /**
     * Удаляет администоратора по Id
     */
    public function deleteAdmin($id) {
        $pdo = \App\Config::pdo();
        $sql = "DELETE FROM admins WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([$id]);

        if (!$result) {
            return($stmt->errorInfo());
        }
    }

    /**
     * Получает таблицу администраторов
     */
    public function getAdminsList() {
        $pdo = \App\Config::pdo();
        $sql = "SELECT login, id FROM admins";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute();

        if (!$result) {
            return($stmt->errorInfo());
        }
        return $stmt->fetchAll();
    }

    /**
     * Получает Id администратора по логину
     */
    function getAdminByLogin($login) {
        $pdo = \App\Config::pdo();
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

    /**
     * Получает администратора, сохраненного в параметрах сеанса
     */
    function getAdminFromGlobals() {
        if (isset($_SESSION['admin'])) {
            return $_SESSION['admin'];
        } elseif (isset($_COOKIE['admin'])) {
            return $_COOKIE['admin'];
        } else {
            return '';
        }
    }

    /**
     * Сохраняет администратора, параметрах сеанса
     */
    function putAdminToGlobals($admin) {
        setcookie('admin', $admin, time() + 3600);
        $_SESSION['admin'] = $admin;
    }

}
