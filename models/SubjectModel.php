<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class SubjectModel
{
    public function getAllSubjects()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM subiect ORDER BY name ASC";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        $db->closeConnection();
        return $rows;
    }

    public function getSubject($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM subiect WHERE id = " . (int)$id;
        $result = $db->query($sql);
        $row = $db->fetchAll($result);
        $db->closeConnection();
        return $row[0] ?? false;
    }

    public function saveSubject($id, $name)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $name = addslashes($name);

        if (empty($id)) {
            $sql = "INSERT INTO subiect (name) VALUES ('$name')";
        } else {
            $sql = "UPDATE subiect SET name = '$name' WHERE id = $id";
        }

        $db->query($sql);
        $db->closeConnection();
    }

    public function deleteSubject($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "DELETE FROM subiect WHERE id = " . (int)$id;
        $db->query($sql);
        $db->closeConnection();
    }
}

