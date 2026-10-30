<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.


class ClassModel
{
    public function getAllClasses()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM class ORDER BY name ASC";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        $db->closeConnection();
        return $rows;
    }

    public function getClass($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM class WHERE id_class = " . (int)$id;
        $result = $db->query($sql);
        $row = $db->fetchAll($result);
        $db->closeConnection();
        return $row[0] ?? false;
    }

    public function saveClass($id, $name, $description)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $name = addslashes($name);
        $description = addslashes($description);

        if (empty($id)) {
            $sql = "INSERT INTO class (name, description) VALUES ('$name', '$description')";
        } else {
            $sql = "UPDATE class SET name = '$name', description = '$description' WHERE id_class = $id";
        }

        $db->query($sql);
        $db->closeConnection();
    }
    
    //analogiczna jak w usermodel.php
    public function deleteUser($userId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $userId = intval($userId);

        // 1. Usuń wszystkie oceny ucznia
        $db->query("DELETE FROM student_class_test WHERE student_id = $userId");

        // 2. Usuń wszystkie przesłane prace ucznia (rekurencyjnie)
        $studentDir = __DIR__ . "/../public/works/" . $userId;

        if (is_dir($studentDir)) {
            //TODO NIE DZIAŁA: $this->rrmdir($studentDir);
        }

        // 3. Usuń wpisy z task_submission
        $db->query("DELETE FROM task_submission WHERE student_id = $userId");

        // 4. Usuń użytkownika
        $db->query("DELETE FROM user WHERE user_id = $userId");

        $db->closeConnection();
    }

    public function deleteClass($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $classId = intval($id);

        // 1. Pobierz wszystkich uczniów w klasie
        $sql = "SELECT user_id FROM user WHERE class = $classId";
        $res = $db->query($sql);

        $students = [];
        while ($row = $db->fetch($res)) {
            $students[] = $row['user_id'];
        }

        // 2. Zamknij połączenie — deleteUser otwiera własne
        $db->closeConnection();

        // 3. Usuń każdego ucznia tak jak pojedynczo
        foreach ($students as $studentId) {
            $this->deleteUser($studentId);
        }

        // 4. Usuń wyniki klasy
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $db->query("DELETE FROM student_class_test WHERE class_id = $classId");

        // 5. Usuń przypisania testów do klasy
        $db->query("DELETE FROM test_class WHERE class_id = $classId");

        // 6. Usuń samą klasę
        $db->query("DELETE FROM class WHERE id_class = $classId");

        $db->closeConnection();
    }

}

