<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class ClassResultsController
{
    public function handleRequest()
    {
        if (!$this->checkUserAuthentication()) {
            header('Location: index.php?action=login');
            exit;
        }

        if (!isTeacher()) {
            header('Location: index.php?action=login');
            exit;
        }

        $model = new ClassResultsModel();

        $selectedClass = $_GET['classId'] ?? '';
        $classes = $model->getAllClasses();

        // Obsługa usuwania oceny
        if (!empty($_GET['delete'])) {
            $id = intval($_GET['delete']);
            $model->deleteTestResultById($id);
        }
        
        // Ustawienie testu jako aktywny dzisiaj
        if (!empty($_GET['activateTest'])) {
            $testId = intval($_GET['activateTest']);
            $classId = intval($_GET['classId']);
            $model->activateTestToday($testId, $classId);
            header("Location: index.php?action=class_results&classId=$classId");
            exit;
        }

        // Wyczyść wyniki testu dla klasy
        if (!empty($_GET['clearTestResults'])) {
            $testId = intval($_GET['clearTestResults']);
            $classId = intval($_GET['classId']);
            $model->clearTestResultsForClass($testId, $classId);
            header("Location: index.php?action=class_results&classId=$classId");
            exit;
        }

        $resultsData = null;
        if (!empty($selectedClass)) {
            $resultsData = $model->getTestResultsForClass($selectedClass);
        }

        include 'views/class_results.php';
    }

    private function checkUserAuthentication()
    {
        return isset($_SESSION['user_id']);
    }
}

