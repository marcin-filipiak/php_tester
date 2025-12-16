<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class SubjectModel
{
    public function getAllSubjects()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM subiect ORDER BY name ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        $db->closeConnection();
        return $rows;
    }

    public function getSubject($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM subiect WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $db->closeConnection();
        return $row ?: false;
    }

    public function saveSubject($id, $name)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if (empty($id)) {
            // INSERT
            $stmt = $db->prepare("INSERT INTO subiect (name) VALUES (?)");
            $stmt->bind_param("s", $name);
        } else {
            // UPDATE
            $stmt = $db->prepare("UPDATE subiect SET name = ? WHERE id = ?");
            $stmt->bind_param("si", $name, $id);
        }

        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function deleteSubject($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("DELETE FROM subiect WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }
}
