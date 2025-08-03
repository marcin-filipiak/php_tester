<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class ClassesController
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
                $this->listClasses();
                break;

            case 'edit':
            case 'add':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->saveClass();
                } else {
                    $this->showClassForm();
                }
                break;

            case 'delete':
                $this->deleteClass();
                break;

            default:
                echo "Nieznana operacja.";
        }
    }

    private function listClasses()
    {
        $model = new ClassModel();
        $classList = $model->getAllClasses();
        include 'views/classes.php';
    }

    private function showClassForm()
    {
        $id = $_GET['id'] ?? '';
        $model = new ClassModel();

        if ($id) {
            $classData = $model->getClass($id);
        }

        if (empty($classData)) {
            $classData = [
                'id_class' => '',
                'name' => '',
                'description' => ''
            ];
        }

        include 'views/class_editor.php';
    }

    private function saveClass()
    {
        $id = $_POST['id_class'];
        $name = $_POST['name'];
        $description = $_POST['description'];

        $model = new ClassModel();
        $model->saveClass($id, $name, $description);

        header('Location: index.php?action=classes');
        exit;
    }

    private function deleteClass()
    {
        $id = $_GET['id'] ?? '';
        if ($id) {
            $model = new ClassModel();
            $model->deleteClass($id);
        }

        header('Location: index.php?action=classes');
        exit;
    }

    private function checkUserAuthentication()
    {
        return isset($_SESSION['user_id']);
    }
}

