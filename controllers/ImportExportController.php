<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class ImportExportController
{
    public function handleRequest()
    {
        if (!$this->checkUserAuthentication() || !isTeacher()) {
            header('Location: index.php?action=login');
            exit;
        }

        $op = $_GET['op'] ?? 'show';
        $model = new ImportExportModel();

        switch ($op) {
            case 'export':
                $this->exportJSON($model);
                break;
            case 'import':
                $this->importJSON($model);
                break;
            case 'show':
            default:
                $testId = $_GET['test_id'] ?? null;
                $tests = $model->getAllTests();
                include 'views/import_export.php';
                break;
        }
    }

    private function exportJSON($model)
    {
        $testId = intval($_POST['test_id']);
        $data = $model->getTestWithQuestionsAndAnswers($testId);

        header('Content-Disposition: attachment; filename="test_' . $testId . '.json"');
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function importJSON($model)
    {
        $testId = intval($_POST['test_id']);
        $json = '';

        if (!empty($_FILES['json_file']['tmp_name'])) {
            $json = file_get_contents($_FILES['json_file']['tmp_name']);
        } elseif (!empty($_POST['json_text'])) {
            $json = $_POST['json_text'];
        }

        $data = json_decode($json, true);
        if (!$data) {
            die("Błąd w formacie JSON");
        }

        $model->importQuestionsAndAnswers($testId, $data);
        header("Location: index.php?action=importexport&test_id=$testId");
        exit;
    }

    private function checkUserAuthentication()
    {
        return isset($_SESSION['user_id']);
    }
}

