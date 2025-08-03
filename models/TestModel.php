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

        $name = addslashes($name);
        $description = addslashes($description);

        if (empty($testId)) {
            $sql = "INSERT INTO `test` 
                    (`name`, `published`, `questionrand`, `number_per_student`, `description`, `id_subiect`, `u_id`, `shared`) 
                    VALUES 
                    ('$name', $published, $questionrand, $number_per_student, '$description', $id_subiect, $u_id, $shared)";
        } else {
            $sql = "UPDATE `test` 
                    SET 
                        `name` = '$name',
                        `published` = $published,
                        `questionrand` = $questionrand,
                        `number_per_student` = $number_per_student,
                        `description` = '$description',
                        `id_subiect` = $id_subiect,
                        `u_id` = $u_id,
                        `shared` = $shared
                    WHERE `id` = $testId";
        }

        $db->query($sql);
        $db->closeConnection();
    }

    // Pobierz dane testu
    public function getTestContent($testId)
    {
        if (!empty($testId)) {
            $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            $sql = "SELECT * FROM `test` WHERE `id` = '$testId'";
            $result = $db->query($sql);
            $row = $db->fetchAll($result);
            $db->closeConnection();

            if (empty($row)) {
                return false;
            } else {
                return $row[0];
            }
        } else {
            return false;
        }
    }

    // Usuń test i przypisania
    public function deleteTest($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        //przypisania do klas
        $sqlAssign = "DELETE FROM test_class WHERE test_id = $testId";
        $db->query($sqlAssign);

        //odpowiedzi do pytań testu
        $sqlTest = "DELETE a FROM answers a JOIN questions q ON a.question_id = q.id WHERE q.test_id ='$testId'";
        $db->query($sqlTest);

        //pytania testu
        $sqlTest = "DELETE FROM questions WHERE test_id='$testId'";
        $db->query($sqlTest);

        //sam test
        $sqlTest = "DELETE FROM `test` WHERE `id`='$testId'";
        $db->query($sqlTest);

        //oceny w klasach dla tego testu
        $sqlTest = "DELETE FROM `student_class_test` WHERE `test_id`='$testId'";
        $db->query($sqlTest);

        $db->closeConnection();
    }

    // Pobierz listę testów
    public function getTestsList()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT test.*, subiect.name AS subiect_name FROM test JOIN subiect ON test.id_subiect = subiect.id ORDER BY subiect_name;";
        $result = $db->query($sql);
        $row = $db->fetchAll($result);
        $db->closeConnection();

        if (empty($row)) {
            return false;
        } else {
            return $row;
        }
    }

    // Pobierz przedmioty
    public function getAllSubiects()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT `id`, `name` FROM `subiect` ORDER BY `name` ASC";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        $db->closeConnection();
        return $rows;
    }

    // Pobierz listę klas
    public function getAllClasses()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT `id_class`, `name` FROM `class` ORDER BY `name` ASC";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        $db->closeConnection();
        return $rows;
    }

    // Pobierz przypisania klas dla testu
    public function getAllClassAssignmentsForTest($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT tc.class_id, c.name AS class_name, tc.test_end 
                FROM test_class tc
                JOIN class c ON c.id_class = tc.class_id
                WHERE tc.test_id = $testId";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
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
            $removeId = (int)$removeId;
            $sql = "DELETE FROM test_class WHERE test_id = $testId AND class_id = $removeId";
            $db->query($sql);
        }

        // Aktualizuj daty przypisań
        foreach ($classDates as $classId => $date) {
            $classId = (int)$classId;
            $date = addslashes($date);
            $sql = "UPDATE test_class SET test_end = '$date' WHERE test_id = $testId AND class_id = $classId";
            $db->query($sql);
        }

        // Dodaj nowe przypisania (pomijaj te usunięte)
        foreach ($newClassDates as $classId => $date) {
            echo "dodaje przypisanie";
            if (in_array($classId, $removeClassIds)) continue;
            $classId = (int)$classId;
            $date = addslashes($date);
            $sql = "INSERT INTO test_class (test_id, class_id, test_end) VALUES ($testId, $classId, '$date')";
            $db->query($sql);
        }

        $db->closeConnection();
    }

    // Sprawdź czy istnieje przypisanie testu do klasy
    public function assignmentExists($testId, $classId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT COUNT(*) AS cnt FROM test_class WHERE test_id = $testId AND class_id = $classId";
        $result = $db->query($sql);
        $row = $db->fetchAll($result);
        $db->closeConnection();
        return !empty($row) && $row[0]['cnt'] > 0;
    }

    // Dodaj nowe przypisanie testu do klasy
    public function addAssignment($testId, $classId, $date)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $date = addslashes($date);
        $sql = "INSERT INTO test_class (test_id, class_id, test_end) VALUES ($testId, $classId, '$date')";
        $db->query($sql);
        $db->closeConnection();
    }
    
    public function addClassAssignment($testId, $classId, $assignmentDate)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $testId = (int)$testId;
        $classId = (int)$classId;
        $assignmentDate = addslashes($assignmentDate);

        // Sprawdź, czy już istnieje przypisanie (opcjonalnie)
        $sqlCheck = "SELECT COUNT(*) as count FROM test_class WHERE test_id = $testId AND class_id = $classId";
        $resultCheck = $db->query($sqlCheck);
        $row = $db->fetchAll($resultCheck);
        if ($row[0]['count'] > 0) {
            $db->closeConnection();
            return; // lub zaktualizuj, jeśli chcesz
        }

        $sql = "INSERT INTO test_class (test_id, class_id, test_end) VALUES ($testId, $classId, '$assignmentDate')";
        $db->query($sql);
        echo $sql;
        $db->closeConnection();
    }
}

