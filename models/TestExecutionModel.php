<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class TestExecutionModel
{

    public function getAvailableTestsForStudent($studentId, $classId)
    {
        $studentId = (int)$studentId;
        $classId = (int)$classId;

        if (!$studentId || !$classId) {
            return [];
        }

        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $today = date('Y-m-d');

        $sql = "
            SELECT t.*, tc.test_end,
                   (
                     SELECT COUNT(*) 
                     FROM student_class_test sct 
                     WHERE sct.student_id = $studentId 
                       AND sct.test_id = t.id 
                       AND sct.class_id = $classId
                   ) AS attempts
            FROM test_class tc
            JOIN test t ON tc.test_id = t.id
            WHERE tc.class_id = $classId
              AND t.published = 1
              AND tc.test_end >= '$today'
        ";

        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        $available = [];

        foreach ($rows as $row) {
            $limit = (int)$row['number_per_student'];
            $attempts = (int)$row['attempts'];
            if ($limit === 0 || $attempts < $limit) {
                $available[] = $row;
            }
        }

        $db->closeConnection();
        return $available;
    }

    public function getTestById($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $res = $db->query("SELECT * FROM test WHERE id = $testId");
        $row = $db->fetchAll($res);
        $db->closeConnection();
        return $row[0] ?? null;
    }

    public function getRandomQuestionsForTest($testId, $limit)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM questions WHERE test_id = $testId ORDER BY RAND() LIMIT $limit";
        $res = $db->query($sql);
        $rows = $db->fetchAll($res);
        $db->closeConnection();
        return $rows;
    }

    public function getQuestionsByTest($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM questions WHERE test_id = $testId";
        $res = $db->query($sql);
        $rows = $db->fetchAll($res);
        $db->closeConnection();
        return $rows;
    }

    public function getAnswersByQuestion($questionId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM answers WHERE question_id = $questionId";
        $res = $db->query($sql);
        $rows = $db->fetchAll($res);
        $db->closeConnection();
        return $rows;
    }

    public function saveStudentTestResult($studentId, $classId, $testId, $result, $maxpoints)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $result = (int)$result;
        $maxpoints = (int)$maxpoints;
        $sql = "INSERT INTO student_class_test (student_id, class_id, test_id, result, test_date, maxpoints) 
                VALUES ($studentId, $classId, $testId, $result, NOW(), $maxpoints)";
        $db->query($sql);
        $db->closeConnection();
    }

    public function getQuestionsByIds(array $ids)
    {
        if (empty($ids)) return [];

        $ids = array_map('intval', $ids); // bezpieczeństwo
        $idList = implode(',', $ids);

        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM questions WHERE id IN ($idList)";
        $res = $db->query($sql);
        $rows = $db->fetchAll($res);
        $db->closeConnection();
        return $rows;
    }


}


