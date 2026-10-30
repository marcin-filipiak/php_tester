<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.


class ImportExportModel
{
    public function getAllTests()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $res = $db->query("SELECT id, name FROM test ORDER BY name");
        $rows = $db->fetchAll($res);
        $db->closeConnection();
        return $rows;
    }

    public function getTestWithQuestionsAndAnswers($testId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $sql = "SELECT * FROM questions WHERE test_id = " . intval($testId);
        $qResult = $db->query($sql);
        $questions = [];

        while ($qRow = $qResult->fetch_assoc()) {
            $qId = $qRow['id'];
            $aSql = "SELECT * FROM answers WHERE question_id = " . intval($qId);
            $aResult = $db->query($aSql);
            $answers = [];

            while ($aRow = $aResult->fetch_assoc()) {
                $answers[] = [
                    'content' => $aRow['content'],
                    'points' => $aRow['points']
                ];
            }

            $questions[] = [
                'content' => $qRow['content'],
                'answers' => $answers
            ];
        }

        $db->closeConnection();
        return $questions;
    }

    public function importQuestionsAndAnswers($testId, $data)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        foreach ($data as $q) {
            $qContent = addslashes($q['content']);
            $db->query("INSERT INTO questions (test_id, content) VALUES ($testId, '$qContent')");
            $questionId = $db->getInsertId();

            foreach ($q['answers'] as $a) {
                $aContent = addslashes($a['content']);
                $points = intval($a['points']);
                $db->query("INSERT INTO answers (question_id, content, points) VALUES ($questionId, '$aContent', $points)");
            }
        }

        $db->closeConnection();
    }
}

