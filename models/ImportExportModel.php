<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class ImportExportModel
{
    public function getAllTests()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT id, name FROM test ORDER BY name");
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

    public function getTestWithQuestionsAndAnswers($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Pobierz pytania
        $stmt = $db->prepare("SELECT id, content FROM questions WHERE test_id = ?");
        $stmt->bind_param("i", $testId);
        $stmt->execute();
        $qResult = $stmt->get_result();
        $questions = [];

        while ($qRow = $qResult->fetch_assoc()) {
            $qId = $qRow['id'];

            // Pobierz odpowiedzi do pytania
            $aStmt = $db->prepare("SELECT content, points FROM answers WHERE question_id = ?");
            $aStmt->bind_param("i", $qId);
            $aStmt->execute();
            $aResult = $aStmt->get_result();
            $answers = [];

            while ($aRow = $aResult->fetch_assoc()) {
                $answers[] = [
                    'content' => $aRow['content'],
                    'points' => (int)$aRow['points']
                ];
            }
            $aStmt->close();

            $questions[] = [
                'content' => $qRow['content'],
                'answers' => $answers
            ];
        }

        $stmt->close();
        $db->closeConnection();
        return $questions;
    }

    public function importQuestionsAndAnswers($testId, $data)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Begin transaction (opcjonalne – można dodać później)
        // $db->getConnection()->autocommit(FALSE);

        foreach ($data as $q) {
            // Wstaw pytanie
            $qStmt = $db->prepare("INSERT INTO questions (test_id, content) VALUES (?, ?)");
            $qStmt->bind_param("is", $testId, $q['content']);
            $qStmt->execute();
            $questionId = $db->getInsertId();
            $qStmt->close();

            // Wstaw odpowiedzi
            foreach ($q['answers'] as $a) {
                $aStmt = $db->prepare("INSERT INTO answers (question_id, content, points) VALUES (?, ?, ?)");
                $points = (int)$a['points'];
                $aStmt->bind_param("isi", $questionId, $a['content'], $points);
                $aStmt->execute();
                $aStmt->close();
            }
        }

        // Commit (jeśli używasz transakcji)
        // $db->getConnection()->commit();
        // $db->getConnection()->autocommit(TRUE);

        $db->closeConnection();
    }
}
