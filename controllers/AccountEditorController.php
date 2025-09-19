<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class AccountEditorController
{
    public function handleRequest()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = $this->processPasswordChange();
            if ($success) {
                $message = "Hasło zostało zmienione.";
                $this->displayForm($message);
            } else {
                $errorMessage = "Błąd podczas zmiany hasła.";
                $this->displayForm("", $errorMessage);
            }
        } else {
            $this->displayForm();
        }
    }

    private function displayForm($message = "", $errorMessage = "")
    {
        include 'views/account_editor.php';
    }

    private function processPasswordChange()
    {
        $currentPassword = $_POST['current-password'];
        $newPassword = $_POST['new-password'];
        $confirmPassword = $_POST['confirm-password'];

        if ($newPassword !== $confirmPassword) {
            return false;
        }

        $userModel = new UserModel();
        $user = $userModel->getUser($_SESSION['user_id']);

        if (!$user) {
            return false;
        }

        // weryfikacja starego hasła
        if (!password_verify($currentPassword . $user['salt'], $user['password'])) {
            return false;
        }

        // zmiana hasła
        $userModel->resetPassword($_SESSION['user_id'], $newPassword);
        return true;
    }
}

