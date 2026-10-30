<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class StudentTestsController
{
    public function handleRequest()
    {
        if (!isset($_SESSION['user_id']) || ($_SESSION['function'] ?? null) != 0) {
            header('Location: index.php?action=login');
            exit;
        }

        $op = $_GET['op'] ?? 'list';

        switch ($op) {
            case 'start':
                $this->startTest();
                break;
            case 'submit':
                $this->submitTest();
                break;
            default:
                $this->listAvailableTests();
        }
    }

    private function listAvailableTests()
    {
        $user = new UserModel();
        $studentId = $_SESSION['user_id'];
        $classId = $user->getUser($studentId)["class"] ?? null;

        if ($classId !== null) {
            $model = new TestExecutionModel();
            $tests = $model->getAvailableTestsForStudent($studentId, $classId);
        }

        include 'views/tests_available.php';
    }

private function startTest()
{
    $testId = (int)($_GET['testId'] ?? 0);
    if (!$testId) {
        echo "Błędny ID testu.";
        return;
    }

    $model = new TestExecutionModel();
    $test = $model->getTestById($testId);

    // Sprawdź, czy pytania już były wylosowane w tej sesji
    $sessionKey = "selected_questions_$testId";

    if (!isset($_SESSION[$sessionKey])) {
        // Losujemy pytania tylko raz
        $questions = $model->getRandomQuestionsForTest($testId, $test['questionrand']);
        $_SESSION[$sessionKey] = array_column($questions, 'id');
    } else {
        // Pobierz pytania po ID zapisanych w sesji
        $questionIds = $_SESSION[$sessionKey];
        $questions = $model->getQuestionsByIds($questionIds);
    }

    include 'views/test_execute.php';
}


    private function submitTest()
    {
        $studentId = $_SESSION['user_id'];
        $classId = (new UserModel())->getUser($studentId)['class'] ?? null;
        $testId = (int)($_POST['testId'] ?? 0);

        if (!$testId || !$classId) {
            echo "Błędne dane.";
            return;
        }

        $sessionKey = "selected_questions_$testId";
        $questionIds = $_SESSION[$sessionKey] ?? [];

        if (empty($questionIds)) {
            echo "Brak danych o wylosowanych pytaniach.";
            return;
        }

        $model = new TestExecutionModel();
        $questions = $model->getQuestionsByIds($questionIds);

        $result = 0;
        $maxpoints = 0;

        foreach ($questions as $q) {
            $qid = $q['id'];
            $isInput = strpos($q['content'], '{?}') !== false;
            $answers = $model->getAnswersByQuestion($qid);

            if ($isInput) {
                $userInput = trim($_POST['question_' . $qid] ?? '');
                $correctAnswer = null;

                foreach ($answers as $a) {
                    if ((int)$a['points'] > 0) {
                        $correctAnswer = trim($a['content']);
                        $maxpoints += $a['points'];
                        break;
                    }
                }

                if ($correctAnswer !== null) {
                    if (mb_strtolower($userInput) === mb_strtolower($correctAnswer)) {
                        $result += 1;
                    }
                }
            } else {
                $selected = $_POST['question_' . $qid] ?? [];
                $selected = is_array($selected) ? $selected : [$selected];

                foreach ($answers as $a) {
                    $maxpoints += max($a['points'], 0);
                    if (in_array($a['id'], $selected)) {
                        $result += $a['points'];
                    }
                }
            }
        }

        $model->saveStudentTestResult($studentId, $classId, $testId, $result, $maxpoints);

        // Sprzątamy sesję z wylosowanymi pytaniami
        unset($_SESSION['selected_questions']);
        unset($_SESSION[$sessionKey]);

        include 'views/test_end.php';
    }
}

