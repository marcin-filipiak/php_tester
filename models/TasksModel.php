<?php

// =====================================================
// TESTER - TasksModel
// Zarządzanie zadaniami, klasami i pracami uczniów
// =====================================================

class TasksModel
{
    // ==========================================
    //                 ZADANIA
    // ==========================================

    /**
     * Pobiera wszystkie zadania, opcjonalnie filtrowane po przedmiocie.
     */
    public function getAllTasks($subiect_id = null)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $sql = "SELECT t.*, s.name AS subiect_name
                FROM tasks t
                LEFT JOIN subiect s ON t.subiect_id = s.id";

        if ($subiect_id !== null) {
            $sql .= " WHERE t.subiect_id = " . (int)$subiect_id;
        }

        $sql .= " ORDER BY t.title ASC";

        $result = $db->query($sql);
        $rows = $db->fetchAll($result);

        $db->closeConnection();
        return $rows;
    }

    /**
     * Pobiera wszystkie przedmioty.
     */
    public function getAllSubiects()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM subiect ORDER BY name ASC";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        $db->closeConnection();
        return $rows;
    }

    /**
     * Pobiera wszystkie klasy.
     */
    public function getAllClasses()
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM class ORDER BY name ASC";
        $result = $db->query($sql);
        $rows = $db->fetchAll($result);
        $db->closeConnection();
        return $rows;
    }

    /**
     * Pobiera jedno konkretne zadanie po ID.
     */
    public function getTask($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "SELECT * FROM tasks WHERE id = " . (int)$id;
        $result = $db->query($sql);
        $row = $db->fetchAll($result);
        $db->closeConnection();
        return $row[0] ?? false;
    }

    /**
     * Tworzy nowe zadanie lub aktualizuje istniejące.
     */
    public function saveTask($id, $subiect_id, $title, $content, $u_id = null)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $title = addslashes($title);
        $content = addslashes($content);
        $u_id = $u_id !== null ? (int)$u_id : "NULL";

        if (empty($id)) {
            // NOWE ZADANIE
            $sql = "INSERT INTO tasks (subiect_id, title, content, u_id)
                    VALUES (" . (int)$subiect_id . ", '$title', '$content', $u_id)";
            $db->query($sql);
            $id = $db->link->insert_id;
        } else {
            // AKTUALIZACJA ISTNIEJĄCEGO ZADANIA
            $sql = "UPDATE tasks
                    SET subiect_id = " . (int)$subiect_id . ",
                        title = '$title',
                        content = '$content',
                        u_id = $u_id
                    WHERE id = " . (int)$id;
            $db->query($sql);
        }

        $db->closeConnection();
        return $id;
    }

    /**
     * Usuwa zadanie i powiązania z klasami oraz wpisy prac uczniów (pliki pozostają).
     */
    public function deleteTask($id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $db->query("DELETE FROM task_class WHERE task_id = " . (int)$id);
        $db->query("DELETE FROM task_submission WHERE task_id = " . (int)$id);
        $db->query("DELETE FROM tasks WHERE id = " . (int)$id);

        $db->closeConnection();
    }

    // ==========================================
    //           PRZYPISANIE DO KLAS
    // ==========================================

    /**
     * Przypisuje zadanie do klasy uczniów z opcjonalną datą oddania.
     * Jeśli klasa jest już przypisana, aktualizuje tylko termin.
     */
    public function assignClassToTask($task_id, $class_id, $due_date = null)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $task_id = (int)$task_id;
        $class_id = (int)$class_id;
        $due_date = trim((string)$due_date);
        $due_date_sql = $due_date !== '' ? "'" . addslashes($due_date) . "'" : "NULL";

        $result = $db->query("SELECT id FROM task_class
                              WHERE task_id = $task_id
                                AND class_id = $class_id
                              LIMIT 1");

        if ($db->fetch($result)) {
            $sql = "UPDATE task_class
                    SET due_date = $due_date_sql
                    WHERE task_id = $task_id
                      AND class_id = $class_id";
        } else {
            $sql = "INSERT INTO task_class (task_id, class_id, due_date)
                    VALUES ($task_id, $class_id, $due_date_sql)";
        }

        $db->query($sql);
        $db->closeConnection();
    }

    /**
     * Aktualizuje termin oddania pracy dla klasy przypisanej do zadania.
     */
    public function updateClassDueDate($task_id, $class_id, $due_date)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $task_id = (int)$task_id;
        $class_id = (int)$class_id;
        $due_date = trim((string)$due_date);
        $due_date_sql = $due_date !== '' ? "'" . addslashes($due_date) . "'" : "NULL";

        $sql = "UPDATE task_class
                SET due_date = $due_date_sql
                WHERE task_id = $task_id
                  AND class_id = $class_id";

        $db->query($sql);
        $db->closeConnection();
    }

    /**
     * Pobiera wszystkie klasy przypisane do zadania.
     */
    public function getClassesForTask($task_id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $sql = "SELECT tc.*, c.name
                FROM task_class tc
                JOIN class c ON tc.class_id = c.id_class
                WHERE tc.task_id = " . (int)$task_id;

        $result = $db->query($sql);
        $rows = $db->fetchAll($result);

        $db->closeConnection();
        return $rows;
    }

    /**
     * Usuwa przypisanie zadania do klasy.
     */
    public function removeClassFromTask($task_id, $class_id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $sql = "DELETE FROM task_class 
                WHERE task_id = " . (int)$task_id . " 
                  AND class_id = " . (int)$class_id;
        $db->query($sql);
        $db->closeConnection();
    }

    // ==========================================
    //               PRACE UCZNIÓW
    // ==========================================

    /**
     * Dodaje lub aktualizuje pracę ucznia.
     */
    public function addSubmission($task_id, $student_id, $filename, $grade = null, $comment = "")
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $task_id = intval($task_id);
        $student_id = intval($student_id);
        $grade_sql = $grade !== null ? (int)$grade : "NULL";
        $comment_sql = $comment !== null ? "'" . addslashes($comment) . "'" : "NULL";

        // Sprawdzenie czy plik już istnieje
        $sql_check = "SELECT id FROM task_submission
                      WHERE task_id = $task_id 
                        AND student_id = $student_id 
                        AND content = '$filename'";
        $result_check = $db->query($sql_check);
        $existing = $db->fetch($result_check);

        if ($existing) {
            // Aktualizacja istniejącego wpisu
            $sql = "UPDATE task_submission 
                    SET grade = $grade_sql,
                        comment = $comment_sql,
                        submitted_on = NOW()
                    WHERE id = " . (int)$existing['id'];
        } else {
            // Nowy wpis
            $sql = "INSERT INTO task_submission (task_id, student_id, content, grade, comment)
                    VALUES ($task_id, $student_id, '$filename', $grade_sql, $comment_sql)";
        }

        $db->query($sql);
        $insertId = $db->getInsertId();
        $db->closeConnection();

        return $insertId;
    }

    /**
     * Pobiera wszystkie przesłane pliki ucznia dla danego zadania.
     */
    public function getUserSubmissions($task_id, $student_id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $task_id = intval($task_id);
        $student_id = intval($student_id);

        $sql = "SELECT *
                FROM task_submission
                WHERE task_id = $task_id AND student_id = $student_id
                ORDER BY submitted_on DESC";

        $result = $db->query($sql);
        $rows = $db->fetchAll($result);

        $db->closeConnection();
        return $rows;
    }

    /**
     * Pobiera ocenę i komentarz nauczyciela dla ucznia.
     */
    public function getSubmissionGrade($taskId, $studentId)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $taskId = intval($taskId);
        $studentId = intval($studentId);

        $sql = "SELECT grade, comment
                FROM task_submission
                WHERE task_id = $taskId AND student_id = $studentId
                LIMIT 1";

        $res = $db->query($sql);
        $row = $db->fetch($res);

        $db->closeConnection();
        return $row ?: ['grade' => '', 'comment' => ''];
    }

    /**
     * Zapisuje ocenę i komentarz do pracy ucznia.
     */
    public function saveSubmissionGrade($taskId, $studentId, $grade, $comment)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $taskId = intval($taskId);
        $studentId = intval($studentId);
        $grade = intval($grade);
        $comment = addslashes($comment);

        $sql = "UPDATE task_submission
                SET grade = $grade, comment = '$comment'
                WHERE task_id = $taskId AND student_id = $studentId";

        $db->query($sql);
        $db->closeConnection();
    }

    /**
     * Usuwa przesłany plik ucznia z bazy i zwraca jego nazwę.
     */
    public function deleteSubmission($submission_id, $student_id)
    {
        $db = new Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

        $submission_id = intval($submission_id);
        $student_id = intval($student_id);

        // Pobranie danych pliku
        $sql = "SELECT *
                FROM task_submission
                WHERE id = $submission_id AND student_id = $student_id
                LIMIT 1";
        $result = $db->query($sql);
        $file = $db->fetch($result);

        if (!$file) {
            $db->closeConnection();
            return false;
        }

        // Usunięcie wpisu z bazy
        $sqlDel = "DELETE FROM task_submission
                   WHERE id = $submission_id
                     AND student_id = $student_id
                   LIMIT 1";
        $db->query($sqlDel);
        $db->closeConnection();

        return $file['content']; // nazwa pliku do usunięcia fizycznego
    }
}

