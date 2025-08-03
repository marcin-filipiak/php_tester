<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class StudentResultsModel
{
    public function getUserTestResultsGroupedBySubiect($userId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $sql = "SELECT 
                    subiect.id AS subiect_id,
                    subiect.name AS subiect_name,
                    test.name AS test_name,
                    student_class_test.result,
                    student_class_test.maxpoints,
                    student_class_test.test_date
                FROM student_class_test
                JOIN test ON student_class_test.test_id = test.id
                JOIN subiect ON test.id_subiect = subiect.id
                WHERE student_class_test.student_id = " . intval($userId) . "
                ORDER BY subiect.name, student_class_test.test_date DESC";

        $result = $db->query($sql);
        $rows = $db->fetchAll($result); // poprawka: zmienna $rows
        $db->closeConnection();

        // Grupowanie po subiect
        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['subiect_name']][] = $row;
        }

        return $grouped;
    }
}

