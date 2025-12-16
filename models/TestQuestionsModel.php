<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class TestQuestionsModel
{
    public function getQuestionsWithAnswers($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Pobierz pytania
        $qStmt = $db->prepare("SELECT * FROM questions WHERE test_id = ?");
        $qStmt->bind_param("i", $testId);
        $qStmt->execute();
        $qResult = $qStmt->get_result();
        $questions = [];
        while ($row = $qResult->fetch_assoc()) {
            $questions[] = $row;
        }
        $qStmt->close();

        // Dla każdego pytania pobierz odpowiedzi
        foreach ($questions as &$question) {
            $aStmt = $db->prepare("SELECT * FROM answers WHERE question_id = ? ORDER BY id ASC");
            $aStmt->bind_param("i", $question['id']);
            $aStmt->execute();
            $aResult = $aStmt->get_result();
            $answers = [];
            while ($aRow = $aResult->fetch_assoc()) {
                $answers[] = $aRow;
            }
            $question['answers'] = $answers;
            $aStmt->close();
        }

        $db->closeConnection();
        return $questions;
    }

    public function getQuestion($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM questions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $db->closeConnection();
        return $row ?: false;
    }

    public function saveQuestion($id, $test_id, $content)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if (empty($id)) {
            $stmt = $db->prepare("INSERT INTO questions (test_id, content) VALUES (?, ?)");
            $stmt->bind_param("is", $test_id, $content);
        } else {
            $stmt = $db->prepare("UPDATE questions SET content = ? WHERE id = ?");
            $stmt->bind_param("si", $content, $id);
        }

        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function deleteQuestion($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        // Usuń odpowiedzi
        $stmt = $db->prepare("DELETE FROM answers WHERE question_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Usuń pytanie
        $stmt = $db->prepare("DELETE FROM questions WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        $db->closeConnection();
    }

    public function getAnswersForQuestion($questionId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM answers WHERE question_id = ? ORDER BY id ASC");
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

    public function getAnswer($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("SELECT * FROM answers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        $db->closeConnection();
        return $row ?: false;
    }

    public function saveAnswer($id, $question_id, $content, $points)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        if (empty($id)) {
            $stmt = $db->prepare("INSERT INTO answers (question_id, content, points) VALUES (?, ?, ?)");
            $stmt->bind_param("isi", $question_id, $content, $points);
        } else {
            $stmt = $db->prepare("UPDATE answers SET content = ?, points = ? WHERE id = ?");
            $stmt->bind_param("sii", $content, $points, $id);
        }

        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }

    public function deleteAnswer($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $stmt = $db->prepare("DELETE FROM answers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $db->closeConnection();
    }
}
