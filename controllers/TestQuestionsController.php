<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.


class TestQuestionsController
{
    public function handleRequest()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $testId = $_GET['testId'] ?? null;
        $op = $_GET['op'] ?? 'list';

        switch ($op) {
            case 'list':
                $this->listQuestions($testId);
                break;
            case 'add':
            case 'edit':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->saveQuestion();
                } else {
                    $this->showQuestionForm();
                }
                break;
            case 'delete':
                $this->deleteQuestion();
                break;
            case 'add_answer':
            case 'edit_answer':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->saveAnswer();
                } else {
                    $this->showAnswerForm();
                }
                break;
            case 'delete_answer':
                $this->deleteAnswer();
                break;
            default:
                echo "Nieznana operacja.";
        }
    }

    private function listQuestions($testId)
    {
        $model = new TestQuestionsModel();
        $questions = $model->getQuestionsWithAnswers($testId);
        include 'views/test_questions.php';
    }

    private function showQuestionForm()
    {
        $questionId = $_GET['questionId'] ?? null;
        $testId = $_GET['testId'];
        $model = new TestQuestionsModel();

        if ($questionId) {
            $questionData = $model->getQuestion($questionId);
        }

        if (empty($questionData)) {
            $questionData = [
                'id' => '',
                'test_id' => $testId,
                'content' => ''
            ];
        }

        include 'views/question_editor.php';
    }

    private function saveQuestion()
    {
        $id = $_POST['questionId'];
        $test_id = $_POST['testId'];
        $content = $_POST['content'];

        $model = new TestQuestionsModel();
        $model->saveQuestion($id, $test_id, $content);

        header("Location: index.php?action=test_questions&testId=$test_id");
        exit;
    }

    private function deleteQuestion()
    {
        $questionId = $_GET['questionId'] ?? null;
        $testId = $_GET['testId'] ?? null;
        if ($questionId) {
            $model = new TestQuestionsModel();
            $model->deleteQuestion($questionId);
        }
        header("Location: index.php?action=test_questions&testId=$testId");
        exit;
    }

    private function showAnswerForm()
    {
        $answerId = $_GET['answerId'] ?? null;
        $questionId = $_GET['questionId'];
        $testId = $_GET['testId'];
        $model = new TestQuestionsModel();

        if ($answerId) {
            $answerData = $model->getAnswer($answerId);
        }

        if (empty($answerData)) {
            $answerData = [
                'id' => '',
                'question_id' => $questionId,
                'content' => '',
                'points' => 0
            ];
        }

        include 'views/answer_editor.php';
    }

    private function saveAnswer()
    {
        $answerId = $_POST['answerId'] ?? null;
        $question_id = $_POST['questionId'];
        $testId = $_POST['testId'];
        $content = $_POST['content'];
        $points = (int) $_POST['points'];

        $model = new TestQuestionsModel();
        $model->saveAnswer($answerId, $question_id, $content, $points);

        header("Location: index.php?action=test_questions&testId=$testId");
        exit;
    }

    private function deleteAnswer()
    {
        $answerId = $_GET['answerId'] ?? null;
        $testId = $_GET['testId'] ?? null;
        if ($answerId) {
            $model = new TestQuestionsModel();
            $model->deleteAnswer($answerId);
        }
        header("Location: index.php?action=test_questions&testId=$testId");
        exit;
    }
}

