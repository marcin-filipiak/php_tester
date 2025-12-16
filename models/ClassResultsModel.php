<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class ClassResultsModel
{
    public function getAllClasses()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("
            SELECT c.id_class, c.name 
            FROM class c
            JOIN user u ON u.class = c.id_class
            GROUP BY c.id_class, c.name
            ORDER BY c.name ASC
        ");
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

    public function getTestResultsForClass($classId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("
            SELECT 
                sct.id,
                user.user_id,
                user.firstname,
                user.lastname,
                test.name AS test_name,
                sct.result,
                test.id AS test_id, 
                sct.maxpoints,
                sct.test_date
            FROM student_class_test sct
            JOIN user ON sct.student_id = user.user_id
            JOIN test ON sct.test_id = test.id
            WHERE user.class = ?
            ORDER BY user.lastname, user.firstname, test.name, sct.test_date DESC
        ");
        $stmt->bind_param("i", $classId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        $db->closeConnection();

        $tests = [];
        $results = [];

        foreach ($rows as $row) {
            $testName = $row['test_name'];
            $testId   = $row['test_id'];
            $userId   = $row['user_id'];

            $tests[$testName] = $testId;

            $results[$userId]['id'] = $userId;
            $results[$userId]['name'] = $row['lastname'] . ' ' . $row['firstname'];
            $results[$userId]['results'][$testName][] = [
                'grade' => gradeFromPoints($row['result'], $row['maxpoints']),
                'maxpoints' => $row['maxpoints'],
                'result' => $row['result'],
                'date' => date('Y-m-d', strtotime($row['test_date'])),
                'id' => $row['id']
            ];
        }

        return ['tests' => $tests, 'results' => $results];
    }

    public function activateTestToday($testId, $classId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $today = date('Y-m-d'); // bezpieczna stała, nie pochodzi od użytkownika

        $stmt = $db->prepare("UPDATE test_class SET test_end = ? WHERE test_id = ? AND class_id = ?");
        $stmt->bind_param("sii", $today, $testId, $classId);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function clearTestResultsForClass($testId, $classId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("DELETE FROM student_class_test WHERE test_id = ? AND class_id = ?");
        $stmt->bind_param("ii", $testId, $classId);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function deleteTestResultById($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("DELETE FROM student_class_test WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }
}
