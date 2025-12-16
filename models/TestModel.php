<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class TestModel
{
    // Zapis testu (dodaj lub edytuj)
    public function saveTest($testId, $name, $published, $questionrand, $number_per_student, $description, $id_subiect, $u_id, $shared)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if (empty($testId)) {
            // INSERT
            $stmt = $db->prepare("INSERT INTO `test` 
                (`name`, `published`, `questionrand`, `number_per_student`, `description`, `id_subiect`, `u_id`, `shared`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("siiisiii", $name, $published, $questionrand, $number_per_student, $description, $id_subiect, $u_id, $shared);
            $stmt->execute();
            $testId = $db->getInsertId();
            $stmt->close();
        } else {
            // UPDATE
            $stmt = $db->prepare("UPDATE `test` 
                SET `name` = ?, `published` = ?, `questionrand` = ?, `number_per_student` = ?, 
                    `description` = ?, `id_subiect` = ?, `u_id` = ?, `shared` = ?
                WHERE `id` = ?");
            $stmt->bind_param("siiisiiii", $name, $published, $questionrand, $number_per_student, $description, $id_subiect, $u_id, $shared, $testId);
            $stmt->execute();
            $stmt->close();
        }

        $db->closeConnection();
        return $testId;
    }

    // Pobierz dane testu
    public function getTestContent($testId)
    {
        if (empty($testId)) {
            return false;
        }

        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM `test` WHERE `id` = ?");
        $stmt->bind_param("i", $testId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $db->closeConnection();
        return $row ?: false;
    }

    // Usuń test i wszystkie powiązane dane
    public function deleteTest($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Usuń oceny uczniów
        $stmt = $db->prepare("DELETE FROM `student_class_test` WHERE `test_id` = ?");
        $stmt->bind_param("i", $testId);
        $stmt->execute();
        $stmt->close();

        // Usuń przypisania do klas
        $stmt = $db->prepare("DELETE FROM `test_class` WHERE `test_id` = ?");
        $stmt->bind_param("i", $testId);
        $stmt->execute();
        $stmt->close();

        // Usuń odpowiedzi (przez JOIN z questions)
        $stmt = $db->prepare("DELETE a FROM answers a JOIN questions q ON a.question_id = q.id WHERE q.test_id = ?");
        $stmt->bind_param("i", $testId);
        $stmt->execute();
        $stmt->close();

        // Usuń pytania
        $stmt = $db->prepare("DELETE FROM `questions` WHERE `test_id` = ?");
        $stmt->bind_param("i", $testId);
        $stmt->execute();
        $stmt->close();

        // Usuń sam test
        $stmt = $db->prepare("DELETE FROM `test` WHERE `id` = ?");
        $stmt->bind_param("i", $testId);
        $stmt->execute();
        $stmt->close();

        $db->closeConnection();
    }

    // Pobierz listę testów
    public function getTestsList()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("
            SELECT test.*, subiect.name AS subiect_name 
            FROM test 
            JOIN subiect ON test.id_subiect = subiect.id 
            ORDER BY subiect_name
        ");
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

    // Pobierz przedmioty
    public function getAllSubiects()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT `id`, `name` FROM `subiect` ORDER BY `name` ASC");
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

    // Pobierz listę klas
    public function getAllClasses()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT `id_class`, `name` FROM `class` ORDER BY `name` ASC");
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

    // Pobierz przypisania klas dla testu
    public function getAllClassAssignmentsForTest($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("
            SELECT tc.class_id, c.name AS class_name, tc.test_end 
            FROM test_class tc
            JOIN class c ON c.id_class = tc.class_id
            WHERE tc.test_id = ?
        ");
        $stmt->bind_param("i", $testId);
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

    // Zapis przypisań klas: usuwanie, edycja dat i dodawanie nowych
    public function saveTestClasses($testId, $classDates, $newClassDates, $removeClassIds)
    {
        if (empty($testId)) return;

        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Usuń zaznaczone przypisania
        foreach ($removeClassIds as $removeId) {
            $stmt = $db->prepare("DELETE FROM test_class WHERE test_id = ? AND class_id = ?");
            $removeId = (int)$removeId;
            $stmt->bind_param("ii", $testId, $removeId);
            $stmt->execute();
            $stmt->close();
        }

        // Aktualizuj daty przypisań
        foreach ($classDates as $classId => $date) {
            $classId = (int)$classId;
            $stmt = $db->prepare("UPDATE test_class SET test_end = ? WHERE test_id = ? AND class_id = ?");
            $stmt->bind_param("sii", $date, $testId, $classId);
            $stmt->execute();
            $stmt->close();
        }

        // Dodaj nowe przypisania
        foreach ($newClassDates as $classId => $date) {
            if (in_array((string)$classId, (array)$removeClassIds)) continue;
            $classId = (int)$classId;
            $stmt = $db->prepare("INSERT INTO test_class (test_id, class_id, test_end) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $testId, $classId, $date);
            $stmt->execute();
            $stmt->close();
        }

        $db->closeConnection();
    }

    // Sprawdź czy istnieje przypisanie testu do klasy
    public function assignmentExists($testId, $classId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM test_class WHERE test_id = ? AND class_id = ?");
        $stmt->bind_param("ii", $testId, $classId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $db->closeConnection();
        return !empty($row) && $row['cnt'] > 0;
    }

    // Dodaj nowe przypisanie testu do klasy
    public function addAssignment($testId, $classId, $date)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("INSERT INTO test_class (test_id, class_id, test_end) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $testId, $classId, $date);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    // Alias (jeśli potrzebny) – lepiej usunąć duplikat lub wywoływać addAssignment
    public function addClassAssignment($testId, $classId, $assignmentDate)
    {
        // Usunięto echo SQL (debug) – niepotrzebne w produkcji
        $this->addAssignment($testId, $classId, $assignmentDate);
    }
}
