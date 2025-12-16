<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class ClassModel
{
    public function getAllClasses()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM class ORDER BY name ASC");
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

    public function getClass($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM class WHERE id_class = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $db->closeConnection();
        return $row ?: false;
    }

    public function saveClass($id, $name, $description)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if (empty($id)) {
            // INSERT
            $stmt = $db->prepare("INSERT INTO class (name, description) VALUES (?, ?)");
            $stmt->bind_param("ss", $name, $description);
        } else {
            // UPDATE
            $stmt = $db->prepare("UPDATE class SET name = ?, description = ? WHERE id_class = ?");
            $stmt->bind_param("ssi", $name, $description, $id);
        }

        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function deleteClass($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Usuń przypisane testy
        $stmt = $db->prepare("DELETE FROM test_class WHERE class_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Usuń wyniki uczniów z tej klasy
        $stmt = $db->prepare("DELETE FROM student_class_test WHERE class_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Usuń uczniów przypisanych do tej klasy
        $stmt = $db->prepare("DELETE FROM user WHERE class = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Usuń klasę
        $stmt = $db->prepare("DELETE FROM class WHERE id_class = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $db->closeConnection();
    }
}
