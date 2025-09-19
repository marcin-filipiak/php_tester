<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class SubjectsController
{
    public function handleRequest()
    {
        if (!$this->checkUserAuthentication()) {
            header('Location: index.php?action=login');
            exit;
        }

        // sprawdzenie czy użytkownik jest nauczycielem
        if (!isTeacher()) {
            header('Location: index.php?action=login');
            exit;
        }

        $op = $_GET['op'] ?? 'list';

        switch ($op) {
            case 'list':
                $this->listSubjects();
                break;

            case 'edit':
            case 'add':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->saveSubject();
                } else {
                    $this->showSubjectForm();
                }
                break;

            case 'delete':
                $this->deleteSubject();
                break;

            default:
                echo "Nieznana operacja.";
        }
    }

    private function listSubjects()
    {
        $model = new SubjectModel();
        $subjectList = $model->getAllSubjects();
        include 'views/subjects.php';
    }

    private function showSubjectForm()
    {
        $id = $_GET['id'] ?? '';
        $model = new SubjectModel();

        if ($id) {
            $subjectData = $model->getSubject($id);
        }

        if (empty($subjectData)) {
            $subjectData = [
                'id' => '',
                'name' => ''
            ];
        }

        include 'views/subject_editor.php';
    }

    private function saveSubject()
    {
        $id = $_POST['id'];
        $name = $_POST['name'];

        $model = new SubjectModel();
        $model->saveSubject($id, $name);

        header('Location: index.php?action=subjects');
        exit;
    }

    private function deleteSubject()
    {
        $id = $_GET['id'] ?? '';
        if ($id) {
            $model = new SubjectModel();
            $model->deleteSubject($id);
        }

        header('Location: index.php?action=subjects');
        exit;
    }

    private function checkUserAuthentication()
    {
        return isset($_SESSION['user_id']);
    }
}

