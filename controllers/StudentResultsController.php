<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class StudentResultsController
{
    public function handleRequest()
    {
        if (!$this->checkUserAuthentication()) {
            header('Location: index.php?action=login');
            exit;
        }

        $this->showUserTests();
    }

    private function showUserTests()
    {
        $userId = $_SESSION['user_id'];
        $model = new StudentResultsModel();
        $userTests = $model->getUserTestResultsGroupedBySubiect($userId);

        include 'views/student_results.php';
    }

    private function checkUserAuthentication()
    {
        return isset($_SESSION['user_id']);
    }
}

