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

        // Zapytanie z podzapytaniem — studentId i classId są już intval(), więc bezpieczne w kontekście LIMIT/IN
        // Ale dla spójności i bezpieczeństwa logicznego, użyjemy prepared statement
        $sql = "
            SELECT t.*, tc.test_end,
                   (
                     SELECT COUNT(*) 
                     FROM student_class_test sct 
                     WHERE sct.student_id = ? 
                       AND sct.test_id = t.id 
                       AND sct.class_id = ?
                   ) AS attempts
            FROM test_class tc
            JOIN test t ON tc.test_id = t.id
            WHERE tc.class_id = ?
              AND t.published = 1
              AND tc.test_end >= ?
        ";

        $stmt = $db->prepare($sql);
        $stmt->bind_param("iiss", $studentId, $classId, $classId, $today);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();

        $available = [];
        foreach ($rows as $row) {
            $limit = (int)($row['number_per_student'] ?? 0);
            $attempts = (int)($row['attempts'] ?? 0);
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
        $stmt = $db->prepare("SELECT * FROM test WHERE id = ?");
        $stmt->bind_param("i", $testId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $db->closeConnection();
        return $row;
    }

    public function getRandomQuestionsForTest($testId, $limit)
    {
        $testId = (int)$testId;
        $limit = (int)$limit;
        // LIMIT nie może być parametryzowany w mysqli, ale intval() zapewnia bezpieczeństwo
        if ($limit <= 0) $limit = 1;
        if ($limit > 100) $limit = 100; // ograniczenie bezpieczeństwa

        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM questions WHERE test_id = ? ORDER BY RAND() LIMIT ?");
        // W mysqli LIMIT wymaga typu "i", ale bind_param obsługuje to
        $stmt->bind_param("ii", $testId, $limit);
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

    public function getQuestionsByTest($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM questions WHERE test_id = ?");
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

    public function getAnswersByQuestion($questionId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM answers WHERE question_id = ? ORDER BY RAND()");
        $stmt->bind_param("i", $questionId);
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

    public function saveStudentTestResult($studentId, $classId, $testId, $result, $maxpoints)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("INSERT INTO student_class_test (student_id, class_id, test_id, result, test_date, maxpoints) 
                              VALUES (?, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param("iiiii", $studentId, $classId, $testId, $result, $maxpoints);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function getQuestionsByIds(array $ids)
    {
        if (empty($ids)) {
            return [];
        }

        // Walidacja: tylko liczby całkowite
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, fn($id) => $id > 0);
        if (empty($ids)) {
            return [];
        }

        // Przygotuj zapytanie z dynamiczną liczbą placeholderów
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "SELECT * FROM questions WHERE id IN ($placeholders)";

        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare($sql);

        // Przygotuj typy: same 'i'
        $types = str_repeat('i', count($ids));
        $stmt->bind_param($types, ...$ids);
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
}
