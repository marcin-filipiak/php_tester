<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class UserModel
{
    public $user_id = 0;
    public $function = null;

    public function verifyLogin($firstName, $lastName, $password)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT `user_id`, `password`, `salt`, `function` FROM `user` WHERE `firstname` = ? AND `lastname` = ?");
        $stmt->bind_param("ss", $firstName, $lastName);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row) {
            if (password_verify($password . $row['salt'], $row['password'])) {
                $this->user_id = $row['user_id'];
                $this->function = $row['function'];
                $db->closeConnection();
                return true;
            }
        }

        $db->closeConnection();
        return false;
    }

    public function register($firstName, $lastName, $password, $class)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $salt = bin2hex(random_bytes(32));
        $hashedPassword = password_hash($password . $salt, PASSWORD_BCRYPT);

        $stmt = $db->prepare("INSERT INTO `user` (`firstname`, `lastname`, `password`, `salt`, `function`, `class`) VALUES (?, ?, ?, ?, 0, ?)");
        $stmt->bind_param("sssss", $firstName, $lastName, $hashedPassword, $salt, $class);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function getUsers($classId = null)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($classId !== null && $classId !== '') {
            $stmt = $db->prepare("SELECT user.*, class.name AS class_name 
                                  FROM user 
                                  LEFT JOIN class ON user.class = class.id_class 
                                  WHERE user.class = ? 
                                  ORDER BY lastname, firstname");
            $stmt->bind_param("i", $classId);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $stmt = $db->prepare("SELECT user.*, class.name AS class_name 
                                  FROM user 
                                  LEFT JOIN class ON user.class = class.id_class 
                                  ORDER BY lastname, firstname");
            $stmt->execute();
            $result = $stmt->get_result();
        }

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
        $db->closeConnection();
        return $users;
    }

    public function getUser($userId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $db->closeConnection();
        return $row; // fetch_assoc() zwraca null, jeśli brak wiersza
    }

    public function updateUser($userId, $firstname, $lastname, $class, $function)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if ($class === '' || $class === null) {
            $stmt = $db->prepare("UPDATE user SET firstname = ?, lastname = ?, class = NULL, `function` = ? WHERE user_id = ?");
            $stmt->bind_param("ssii", $firstname, $lastname, $function, $userId);
        } else {
            $stmt = $db->prepare("UPDATE user SET firstname = ?, lastname = ?, class = ?, `function` = ? WHERE user_id = ?");
            $stmt->bind_param("ssiii", $firstname, $lastname, $class, $function, $userId);
        }

        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function deleteUser($userId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("DELETE FROM user WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function resetPassword($userId, $newPassword)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $salt = bin2hex(random_bytes(8));
        $hashed = password_hash($newPassword . $salt, PASSWORD_DEFAULT);

        $stmt = $db->prepare("UPDATE user SET password = ?, salt = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $hashed, $salt, $userId);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function getAllClasses()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM class ORDER BY name");
        $stmt->execute();
        $result = $stmt->get_result();
        $classes = [];
        while ($row = $result->fetch_assoc()) {
            $classes[] = $row;
        }
        $stmt->close();
        $db->closeConnection();
        return $classes;
    }
}
