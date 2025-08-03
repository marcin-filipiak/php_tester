<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class TestsController
{
    public function handleRequest()
    {
        if (!$this->checkUserAuthentication()) {
            header('Location: index.php?action=login');
            exit;
        }
        
        //sprawdzenie czy jest nauczycielem
        if (!isTeacher()) {
           header('Location: index.php?action=login');
           exit;
        }

        $op = $_GET['op'] ?? 'list';

        switch ($op) {
            case 'list':
                $this->listTests();
                break;

            case 'saveAssignment':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->saveAssignment();
                } else {
                    $this->showTestForm();
                }
                break;
            

            case 'edit':
            case 'add':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->saveTest();
                } else {
                    $this->showTestForm();
                }
                break;

            case 'delete':
                $this->deleteTest();
                break;

            default:
                echo "Nieznana operacja.";
        }
    }

    private function listTests()
    {
        $model = new TestModel();
        $testList = $model->getTestsList();
        include 'views/tests.php';
    }

    private function showTestForm()
    {
        $testId = $_GET['testId'] ?? '';
        $model = new TestModel();

        if ($testId) {
            $testData = $model->getTestContent($testId);
             $allClassAssignments = $model->getAllClassAssignmentsForTest($testId);
        }
        else {
            $allClassAssignments = array();
        }

        if (empty($testData)) {
            $testData = [
                'id' => '',
                'name' => '',
                'published' => 0,
                'questionrand' => 0,
                'number_per_student' => 0,
                'description' => '',
                'id_subiect' => '',
                'shared' => 0
            ];
        }

        $subiects = $model->getAllSubiects();
        $classes = $model->getAllClasses();
       

        include 'views/test_editor.php';
    }

    private function saveTest()
    {
        $testId = $_POST['testId'];
        $name = $_POST['name'];
        $published = isset($_POST['published']) ? 1 : 0;
        $questionrand = $_POST['questionrand'] ?? 0;
        $number_per_student = $_POST['number_per_student'];
        $description = $_POST['description'];
        $id_subiect = $_POST['id_subiect'];
        $shared = isset($_POST['shared']) ? 1 : 0;
        $u_id = $_SESSION['user_id'];

        $model = new TestModel();
        $model->saveTest($testId, $name, $published, $questionrand, $number_per_student, $description, $id_subiect, $u_id, $shared);

        $classDates = $_POST['class_dates'] ?? [];
        $newClassDates = []; // Nie używamy, bo dodawanie jest osobnym formularzem
        $removeClassIds = $_POST['remove_class_ids'] ?? [];

        $model->saveTestClasses($testId, $classDates, $newClassDates, $removeClassIds);

        header('Location: index.php?action=tests&op=edit&testId=' . $testId);
        exit;
    }

    private function saveAssignment()
    {
        $testId = $_POST['test_id'] ?? null;
        $classId = $_POST['class_id'] ?? null;
        $assignmentDate = $_POST['assignment_date'] ?? null;

        if (!$testId || !$classId || !$assignmentDate) {
            // tutaj możesz dodać obsługę błędu lub przekierowanie z komunikatem
            //header('Location: index.php?action=tests&op=edit&testId=' . $testId);
            //exit;
        }

        $model = new TestModel();
        $model->addClassAssignment($testId, $classId, $assignmentDate);
        
        header('Location: index.php?action=tests&op=edit&testId=' . $testId);
        exit;
    }

    private function deleteTest()
    {
        $testId = $_GET['testId'] ?? '';
        if ($testId) {
            $model = new TestModel();
            $model->deleteTest($testId);
        }

        header('Location: index.php?action=tests');
        exit;
    }

    private function checkUserAuthentication()
    {
        return isset($_SESSION['user_id']);
    }
}

