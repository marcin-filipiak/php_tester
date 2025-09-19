<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class TestQuestionsModel
{
    public function getQuestionsWithAnswers($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM questions WHERE test_id = $testId";
        $result = $db->query($sql);
        $questions = $db->fetchAll($result);

        foreach ($questions as &$question) {
            $qId = $question['id'];
            $aResult = $db->query("SELECT * FROM answers WHERE question_id = $qId"); 
            $question['answers'] = $db->fetchAll($aResult);
        }

        $db->closeConnection();
        return $questions;
    }

    public function getQuestion($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $result = $db->query("SELECT * FROM questions WHERE id = $id");
        $row = $db->fetchAll($result);
        $db->closeConnection();
        return $row[0] ?? false;
    }

    public function saveQuestion($id, $test_id, $content)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $content = addslashes($content);

        if (empty($id)) {
            $sql = "INSERT INTO questions (test_id, content) VALUES ($test_id, '$content')";
        } else {
            $sql = "UPDATE questions SET content = '$content' WHERE id = $id";
        }

        $db->query($sql);
        $db->closeConnection();
    }

    public function deleteQuestion($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $db->query("DELETE FROM answers WHERE question_id = $id");
        $db->query("DELETE FROM questions WHERE id = $id");
        $db->closeConnection();
    }
    
    public function getAnswersForQuestion($questionId)
{
    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $sql = "SELECT * FROM answers WHERE question_id = $questionId ORDER BY id ASC";
    $result = $db->query($sql);
    $rows = $db->fetchAll($result);
    $db->closeConnection();
    return $rows;
}

public function getAnswer($id)
{
    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $result = $db->query("SELECT * FROM answers WHERE id = $id");
    $rows = $db->fetchAll($result);
    $db->closeConnection();
    return $rows[0] ?? false;
}

public function saveAnswer($id, $question_id, $content, $points)
{
    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $content = addslashes($content);

    if (empty($id)) {
        $sql = "INSERT INTO answers (question_id, content, points) VALUES ($question_id, '$content', $points)";
    } else {
        $sql = "UPDATE answers SET content = '$content', points = $points WHERE id = $id";
    }

    $db->query($sql);
    $db->closeConnection();
}

public function deleteAnswer($id)
{
    $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $sql = "DELETE FROM answers WHERE id = $id";
    $db->query($sql);
    $db->closeConnection();
}

    
}

