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

    public function deleteClass($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        
        //usuwanie klasy
        $sql = "DELETE FROM class WHERE id_class = " . (int)$id;
        $db->query($sql);
        
        //usuwanie userów z tej klasy
        $sql = "DELETE FROM user WHERE class = " . (int)$id;
        $db->query($sql);
        
        //usuwanie wyników danej klasy
        $sql = "DELETE FROM student_class_test WHERE class_id = " . (int)$id;
        $db->query($sql);
        
        //usuwanie przypisanych testów do klasy
        $sql = "DELETE FROM test_class WHERE class_id = " . (int)$id;
        $db->query($sql);
        
        $db->closeConnection();
    }
}

