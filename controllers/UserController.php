<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class UserController
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
                $this->listUsers();
                break;
            case 'edit':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $this->saveUser();
                } else {
                    $this->showUserForm();
                }
                break;
            case 'delete':
                $this->deleteUser();
                break;
            case 'reset':
                $this->resetPassword();
                break;
            default:
                echo "Nieznana operacja.";
        }
    }

    private function listUsers()
    {
        $model = new UserModel();
        $classId = $_GET['class'] ?? null;
        $users = $model->getUsers($classId);
        $classes = $model->getAllClasses();

        include 'views/users.php';
    }

    private function showUserForm()
    {
        $model = new UserModel();
        $userId = $_GET['userId'] ?? '';
        $user = $model->getUser($userId);
        $classes = $model->getAllClasses();

        include 'views/user_editor.php';
    }

private function saveUser()
{
    $userId = $_POST['user_id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $class = $_POST['class'];
    $function = $_POST['function'];

    $model = new UserModel();
    $model->updateUser($userId, $firstname, $lastname, $class, $function);

    header('Location: index.php?action=users');
    exit;
}

    private function deleteUser()
    {
        $userId = $_GET['userId'] ?? '';
        if ($userId) {
            $model = new UserModel();
            $model->deleteUser($userId);
        }

        header('Location: index.php?action=users');
        exit;
    }

    private function resetPassword()
    {
        $userId = $_GET['userId'] ?? '';
        if ($userId) {
            $model = new UserModel();
            $model->resetPassword($userId, '12345');
        }

        header('Location: index.php?action=users');
        exit;
    }

    private function checkUserAuthentication()
    {
        return isset($_SESSION['user_id']);
    }
}

