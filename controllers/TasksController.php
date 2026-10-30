<?php
// =====================================================
// TESTER - TasksController
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// License: MIT
// =====================================================

class TasksController
{
    // =====================================================
    //                      ROUTER
    // =====================================================

    /**
     * Główna metoda routera dla modułu zadań.
     * Rozdziela logikę dla nauczyciela i ucznia.
     */
    public function handleRequest()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        $op = $_GET['op'] ?? 'list';

        if (isTeacher()) {
            $this->handleTeacherOperations($op);
        } else {
            $this->handleStudentOperations($op);
        }
    }

    // =====================================================
    //                OPERACJE NAUCZYCIELA
    // =====================================================

    private function handleTeacherOperations($op)
    {
        switch ($op) {
            case 'list':
                $this->listTasksForTeacher();
                break;

            case 'edit':
            case 'add':
                $_SERVER['REQUEST_METHOD'] === 'POST' ? $this->saveTask() : $this->showTaskForm();
                break;

            case 'delete':
                $this->deleteTask();
                break;

            case 'saveAssignment':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') $this->saveAssignment();
                break;

            case 'listWorks':
                $this->listWorks();
                break;

            case 'showWork':
                $this->showWork();
                break;

            case 'download':
                $this->downloadWork();
                break;

            case 'viewFile':
                $this->viewFile();
                break;

            default:
                echo "Nieznana operacja.";
        }
    }

    /**
     * Lista zadań nauczyciela.
     */
    private function listTasksForTeacher()
    {
        $model = new TasksModel();
        $tasks = array_filter($model->getAllTasks(), fn($t) => $t['u_id'] == $_SESSION['user_id']);
        include 'views/tasks.php';
    }

    /**
     * Formularz do edycji lub dodania zadania.
     */
    private function showTaskForm()
    {
        $taskId = $_GET['taskId'] ?? '';
        $model = new TasksModel();

        if ($taskId) {
            $taskData = $model->getTask($taskId);
            $assignedClasses = $model->getClassesForTask($taskId);
        } else {
            $taskData = ['id'=>'','subiect_id'=>'','title'=>'','content'=>'','u_id'=>$_SESSION['user_id']];
            $assignedClasses = [];
        }

        $subiects = $model->getAllSubiects();
        $classes = $model->getAllClasses();

        include 'views/tasks_editor.php';
    }

    /**
     * Zapisuje zadanie (edycja lub nowe).
     */
    private function saveTask()
    {
        $taskId = $_POST['taskId'] ?? '';
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $subiect_id = $_POST['subiect_id'] ?? '';
        $u_id = $_SESSION['user_id'];

        $model = new TasksModel();
        $taskId = $model->saveTask($taskId, $subiect_id, $title, $content, $u_id);

        if ($taskId) {
            $assignedClasses = $_POST['assigned_classes'] ?? [];
            $dueDates = $_POST['assigned_classes_dates'] ?? [];
            $removeClasses = $_POST['remove_classes'] ?? [];

            foreach ($assignedClasses as $classId) {
                $model->assignClassToTask($taskId, $classId, $dueDates[$classId] ?? null);
            }

            foreach ($dueDates as $classId => $dueDate) {
                $model->updateClassDueDate($taskId, $classId, $dueDate);
            }

            foreach ($removeClasses as $classId) $model->removeClassFromTask($taskId, $classId);

            header('Location: index.php?action=tasks&op=edit&taskId=' . $taskId);
        } else {
            header('Location: index.php?action=tasks');
        }
        exit;
    }

    /**
     * Usuwa zadanie wraz z katalogiem prac uczniów.
     */
    private function deleteTask()
    {
        $taskId = $_GET['taskId'] ?? '';
        if ($taskId) {
            $model = new TasksModel();
            $model->deleteTask($taskId);
        }

        header('Location: index.php?action=tasks');
        exit;
    }

    /**
     * Rekurencyjne usuwanie katalogu.
     */
    private function deleteDirectoryRecursive($dir)
    {
        if (!is_dir($dir)) return;
        foreach (scandir($dir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = "$dir/$item";
            is_dir($path) ? $this->deleteDirectoryRecursive($path) : unlink($path);
        }
        rmdir($dir);
    }

    /**
     * Przypisuje zadanie do klasy ucznia.
     */
    private function saveAssignment()
    {
        $taskId = $_POST['task_id'] ?? '';
        $classId = $_POST['class_id'] ?? '';
        $dueDate = $_POST['assignment_date'] ?? null;

        if ($taskId && $classId) {
            $model = new TasksModel();
            $model->assignClassToTask($taskId, $classId, $dueDate);
        }

        header('Location: index.php?action=tasks&op=edit&taskId=' . $taskId);
        exit;
    }

    /**
     * Lista uczniów, którzy oddali prace (tylko ci, którzy coś przesłali).
     */
    private function listWorks()
    {
        $taskId = $_GET['taskId'] ?? null;
        if (!$taskId) { echo "Brak ID zadania."; return; }

        $model = new TasksModel();
        $task = $model->getTask($taskId);
        $classes = $model->getClassesForTask($taskId);
        $userModel = new UserModel();

        foreach ($classes as &$cls) {
            $students = $userModel->getStudentsInClass($cls['class_id']);
            $filtered = [];
            foreach ($students as $st) {
                $submissions = $model->getUserSubmissions($taskId, $st['user_id']);
                if (!empty($submissions)) {
                    $st['submissions'] = $submissions;
                    $filtered[] = $st;
                }
            }
            $cls['students'] = $filtered;
        }

        include 'views/task_teacher_works.php';
    }

    /**
     * Widok pracy pojedynczego ucznia + zapis oceny.
     */
    private function showWork()
    {
        $taskId = $_GET['taskId'] ?? null;
        $studentId = $_GET['studentId'] ?? null;

        if (!$taskId || !$studentId) { echo "Błędne dane."; return; }

        $model = new TasksModel();
        $userModel = new UserModel();

        $student = $userModel->getUser($studentId);
        $files = $model->getUserSubmissions($taskId, $studentId);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newGrade = $_POST['grade'] ?? '';
            $comment = $_POST['comment'] ?? '';
            $model->saveSubmissionGrade($taskId, $studentId, $newGrade, $comment);
            header("Location: index.php?action=tasks&op=showWork&taskId=$taskId&studentId=$studentId");
            exit;
        }

        include 'views/task_teacher_show_work.php';
    }

    /**
     * Wysyła plik pracy do pobrania (zamiast otwierania w przeglądarce).
     */
    private function downloadWork()
    {
        $taskId = (int)($_GET['taskId'] ?? 0);
        $studentId = (int)($_GET['studentId'] ?? 0);
        $submissionId = (int)($_GET['submissionId'] ?? 0);

        if (!$taskId || !$studentId || !$submissionId) {
            http_response_code(400);
            echo "Błędne dane.";
            return;
        }

        $model = new TasksModel();
        $userModel = new UserModel();

        $student = $userModel->getUser($studentId);
        if (!$student) {
            http_response_code(404);
            echo "Nie znaleziono ucznia.";
            return;
        }

        //Uczeń musi należeć do klasy przypisanej do zadania
        $allowedClassIds = array_column($model->getClassesForTask($taskId), 'class_id');
        if (!in_array($student['class'] ?? null, $allowedClassIds)) {
            http_response_code(403);
            echo "Brak dostępu do tej pracy.";
            return;
        }

        $fileName = null;
        foreach ($model->getUserSubmissions($taskId, $studentId) as $submission) {
            if ((int)$submission['id'] === $submissionId) $fileName = $submission['content'];
        }

        if ($fileName === null) {
            http_response_code(404);
            echo "Nie znaleziono pliku.";
            return;
        }

        $filePath = __DIR__ . '/../public/works/' . $studentId . '/' . $taskId . '/' . basename($fileName);
        if (!is_file($filePath)) {
            http_response_code(404);
            echo "Plik nie istnieje na serwerze.";
            return;
        }

        $this->sendFileDownload($filePath, $fileName);
    }

    /**
     * Wyświetla plik inline (np. obrazek) w przeglądarce.
     */
    private function viewFile()
    {
        $taskId = (int)($_GET['taskId'] ?? 0);
        $studentId = (int)($_GET['studentId'] ?? 0);
        $submissionId = (int)($_GET['submissionId'] ?? 0);

        if (!$taskId || !$studentId || !$submissionId) {
            http_response_code(400);
            echo "Błędne dane.";
            return;
        }

        $model = new TasksModel();
        $userModel = new UserModel();

        $student = $userModel->getUser($studentId);
        if (!$student) {
            http_response_code(404);
            echo "Nie znaleziono ucznia.";
            return;
        }

        $allowedClassIds = array_column($model->getClassesForTask($taskId), 'class_id');
        if (!in_array($student['class'] ?? null, $allowedClassIds)) {
            http_response_code(403);
            echo "Brak dostępu do tej pracy.";
            return;
        }

        $fileName = null;
        foreach ($model->getUserSubmissions($taskId, $studentId) as $submission) {
            if ((int)$submission['id'] === $submissionId) $fileName = $submission['content'];
        }

        if ($fileName === null) {
            http_response_code(404);
            echo "Nie znaleziono pliku.";
            return;
        }

        $filePath = __DIR__ . '/../public/works/' . $studentId . '/' . $taskId . '/' . basename($fileName);
        if (!is_file($filePath)) {
            http_response_code(404);
            echo "Plik nie istnieje na serwerze.";
            return;
        }

        $mime = 'application/octet-stream';
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detected = finfo_file($finfo, $filePath);
                if ($detected) $mime = $detected;
                finfo_close($finfo);
            }
        } elseif (function_exists('mime_content_type')) {
            $detected = mime_content_type($filePath);
            if ($detected) $mime = $detected;
        }

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $mime);
        header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: inline; filename="' . basename($fileName) . '"');

        readfile($filePath);
        exit;
    }

    /**
     * Uczeń pobiera własny plik pracy.
     */
    private function downloadWorkForStudent()
    {
        $studentId = $_SESSION['user_id'];
        $taskId = (int)($_GET['taskId'] ?? 0);
        $submissionId = (int)($_GET['submissionId'] ?? 0);

        if (!$taskId || !$submissionId) {
            http_response_code(400);
            echo "Błędne dane.";
            return;
        }

        $model = new TasksModel();

        // Uczeń musi mieć dostęp do zadania
        $userModel = new UserModel();
        $classId = $userModel->getUser($studentId)['class'] ?? null;
        $allowedClassIds = array_column($model->getClassesForTask($taskId), 'class_id');
        if (!in_array($classId, $allowedClassIds)) {
            http_response_code(403);
            echo "Brak dostępu do tego zadania.";
            return;
        }

        $fileName = null;
        foreach ($model->getUserSubmissions($taskId, $studentId) as $submission) {
            if ((int)$submission['id'] === $submissionId) $fileName = $submission['content'];
        }

        if ($fileName === null) {
            http_response_code(404);
            echo "Nie znaleziono pliku.";
            return;
        }

        $filePath = __DIR__ . '/../public/works/' . $studentId . '/' . $taskId . '/' . basename($fileName);
        if (!is_file($filePath)) {
            http_response_code(404);
            echo "Plik nie istnieje na serwerze.";
            return;
        }

        $this->sendFileDownload($filePath, $fileName);
    }

    /**
     * Wysyła plik jako załącznik do pobrania.
     */
    private function sendFileDownload($filePath, $fileName)
    {
        $downloadName = preg_replace('/[\x00-\x1F"\\\\\/:*?<>|]+/', '_', basename($fileName));
        if ($downloadName === '') $downloadName = 'plik';

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $downloadName . '"; filename*=UTF-8\'\'' . rawurlencode($downloadName));
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . filesize($filePath));
        header('Expires: 0');
        header('Cache-Control: must-revalidate, no-cache, no-store');
        header('Pragma: public');

        readfile($filePath);
        exit;
    }

    // =====================================================
    //                OPERACJE UCZNIA
    // =====================================================

    /**
     * Lista zadań przypisanych do klasy ucznia.
     */
    private function listTasksForStudent()
    {
        $studentId = $_SESSION['user_id'];
        $userModel = new UserModel();
        $classId = $userModel->getUser($studentId)['class'] ?? null;

        if ($classId === null) { echo "Nie przypisano Ci klasy."; return; }

        $model = new TasksModel();
        $allTasks = $model->getAllTasks();

        $tasks = array_filter($allTasks, function($task) use ($model, $classId) {
            $assignedClasses = $model->getClassesForTask($task['id']);
            foreach ($assignedClasses as $c) {
                if ($c['class_id'] == $classId) return true;
            }
            return false;
        });

        include 'views/tasks_student.php';
    }

    /**
     * Widok zadania ucznia + upload i usuwanie plików.
     */
    private function viewTaskForStudent()
    {
        $studentId = $_SESSION['user_id'];
        $userModel = new UserModel();
        $classId = $userModel->getUser($studentId)['class'] ?? null;

        $taskId = $_GET['taskId'] ?? null;
        if (!$taskId) { echo "Nieprawidłowe ID zadania."; return; }

        $model = new TasksModel();
        $task = $model->getTask($taskId);

        $assignedClasses = $model->getClassesForTask($taskId);
        $allowedClassIds = array_column($assignedClasses, 'class_id');
        if (!in_array($classId, $allowedClassIds)) { echo "Nie masz dostępu do tego zadania."; return; }

        // -----------------------------
        // Upload pliku
        // -----------------------------
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $file = $_FILES['file'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $baseDir = __DIR__ . '/../public/works';
                $userDir = "$baseDir/$studentId";
                $taskDir = "$userDir/$taskId";

                if (!is_dir($userDir)) mkdir($userDir, 0777, true);
                if (!is_dir($taskDir)) mkdir($taskDir, 0777, true);

                $safeName = basename($file['name']);
                $targetPath = "$taskDir/$safeName";

                if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                    $model->addSubmission($taskId, $studentId, $safeName);
                }
            }
            header("Location: index.php?action=tasks&op=view&taskId=$taskId");
            exit;
        }

        // -----------------------------
        // Usuwanie pliku
        // -----------------------------
        if (isset($_GET['deleteFile'])) {
            $submissionId = (int)$_GET['deleteFile'];
            $fileName = $model->deleteSubmission($submissionId, $studentId);
            if ($fileName) {
                $filePath = __DIR__ . "/../public/works/$studentId/$taskId/$fileName";
                if (file_exists($filePath)) unlink($filePath);
            }
            header("Location: index.php?action=tasks&op=view&taskId=$taskId");
            exit;
        }

        // -----------------------------
        // Pobranie plików ucznia
        // -----------------------------
        $files = $model->getUserSubmissions($taskId, $studentId);

        include 'views/task_student_view.php';
    }

// ==========================================
//            OPERACJE UCZNIA - ROUTER
// ==========================================
private function handleStudentOperations($op)
{
    switch ($op) {
        case 'list':
            $this->listTasksForStudent();
            break;

        case 'view':
            $this->viewTaskForStudent();
            break;

        case 'download':
            $this->downloadWorkForStudent();
            break;

        default:
            echo "Nieznana operacja ucznia.";
    }
}

    // =====================================================
    //                  KONIEC KLASA
    // =====================================================
}

