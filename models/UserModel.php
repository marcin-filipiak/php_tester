<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class UserModel
{

    public $user_id = 0;
    public $function = null; 

    // Przykładowa metoda do weryfikacji danych logowania
    public function verifyLogin($firstName, $lastName, $password)
    {
	    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	    // Pobierz hasło i sól z bazy danych na podstawie nazwy użytkownika
	    $sql = "SELECT `user_id`, `password`, `salt`, `function` FROM `user` WHERE `firstname` = '$firstName' AND `lastname` = '$lastName'";
	    $result = $db->query($sql);

	    if ($result && $row = $result->fetch_assoc()) {
		$storedPassword = $row['password'];
		$salt = $row['salt'];

		// Sprawdź, czy hasło jest poprawne przy użyciu funkcji password_verify
		if (password_verify($password . $salt, $storedPassword)){
			$this->user_id = $row['user_id'];
			$this->function = $row['function'];
			return true;
		}
		else {
			return false;
		}
	    }

	    return false;
    }

    public function register($firstName, $lastName, $password, $class)
    {
	$db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	// Sól to losowy ciąg znaków, który jest dodawany do hasła przed haszowaniem
	$salt = bin2hex(random_bytes(32));

	// Haszuj hasło z dodaną solą
	$hashedPassword = password_hash($password . $salt, PASSWORD_BCRYPT);

	$sql = "INSERT INTO `user`(`firstname`, `lastname`, `password`, `salt`, `function`, `class`) 
		VALUES ('$firstName','$lastName','$hashedPassword','$salt',0,'$class')";

	$db->query($sql);
	$db->closeConnection();
    }

public function getUsers($classId = null)
{
    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $sql = "SELECT user.*, class.name AS class_name 
            FROM user 
            LEFT JOIN class ON user.class = class.id_class";
    if ($classId !== null && $classId !== '') {
        $sql .= " WHERE user.class = " . intval($classId);
    }
    $sql .= " ORDER BY lastname, firstname";

    $result = $db->query($sql);
    $users = $db->fetchAll($result);
    $db->closeConnection();
    return $users;
}


    public function getUser($userId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM user WHERE user_id = " . intval($userId);
        $result = $db->query($sql);
        $row = $db->fetchAll($result);
        $db->closeConnection();
        return $row[0] ?? null;
    }

public function updateUser($userId, $firstname, $lastname, $class, $function)
{
    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    
    if ($class === '' || $class === null) {
        $classValue = "NULL";
    } else {
        $classValue = intval($class);
    }
    
    $sql = "UPDATE user 
            SET firstname = '" . addslashes($firstname) . "', 
                lastname = '" . addslashes($lastname) . "', 
                class = $classValue,
                `function` = " . intval($function) . "
            WHERE user_id = " . intval($userId);
    $db->query($sql);
    $db->closeConnection();
}


    public function deleteUser($userId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "DELETE FROM user WHERE user_id = " . intval($userId);
        $db->query($sql);
        $db->closeConnection();
    }

    public function resetPassword($userId, $newPassword)
    {
        $salt = bin2hex(random_bytes(8));
        $hashed = password_hash($newPassword . $salt, PASSWORD_DEFAULT);

        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "UPDATE user 
                SET password = '" . addslashes($hashed) . "', 
                    salt = '$salt' 
                WHERE user_id = " . intval($userId);
        $db->query($sql);
        $db->closeConnection();
    }

    public function getAllClasses()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM class ORDER BY name";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        $db->closeConnection();
        return $rows;
    }


}


