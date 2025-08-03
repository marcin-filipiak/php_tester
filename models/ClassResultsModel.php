<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class ClassResultsModel
{
    public function getAllClasses()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT c.id_class, c.name 
                FROM class c
                JOIN user u ON u.class = c.id_class
                GROUP BY c.id_class, c.name
                ORDER BY c.name ASC";
        $result = $db->query($sql);
        $classes = $db->fetchAll($result);
        $db->closeConnection();
        return $classes;
    }

    public function getTestResultsForClass($classId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "
            SELECT 
                sct.id,
                user.user_id,
                user.firstname,
                user.lastname,
                test.name AS test_name,
                sct.result,
                sct.maxpoints,
                sct.test_date
            FROM student_class_test sct
            JOIN user ON sct.student_id = user.user_id
            JOIN test ON sct.test_id = test.id
            WHERE user.class = " . intval($classId) . "
            ORDER BY user.lastname, user.firstname, test.name, sct.test_date DESC
        ";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        $db->closeConnection();

        $tests = [];
        $results = [];

        foreach ($rows as $row) {
            $testName = $row['test_name'];
            $userId = $row['user_id'];
            $tests[$testName] = true;

            $results[$userId]['id'] = $userId;
            $results[$userId]['name'] = $row['lastname'] . ' ' . $row['firstname'];
            $results[$userId]['results'][$testName][] = [
                'grade' => gradeFromPoints($row['result'], $row['maxpoints']) ,
                'maxpoints' => $row['maxpoints'],
                'result' => $row['result'],
                'date' => date('Y-m-d', strtotime($row['test_date'])),
                'id' => $row['id']
            ];
        }

        return ['tests' => array_keys($tests), 'results' => $results];
    }

    public function deleteTestResultById($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $db->query("DELETE FROM student_class_test WHERE id = ".(int)$id);
        $db->closeConnection();
    }
}

