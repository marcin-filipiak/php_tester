<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class LoginController
{
    public function handleRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLoginForm();
        } else {
            $this->displayLoginForm();
        }
    }

    private function displayLoginForm()
    {
        include 'views/login.php';
    }

    private function processLoginForm()
    {
        // Weryfikacja danych logowania
        $firstName = $_POST['firstname'];
        $lastName = $_POST['lastname'];
        $password = $_POST['password'];

	    $UserModel = new UserModel();

        // Przykładowa weryfikacja - tutaj powinieneś użyć bezpiecznego logowania
        // np. za pomocą hashowania hasła i sprawdzania w bazie danych.
        if ($UserModel->verifyLogin($firstName,$lastName,$password) == true) {
	    // Ustaw sesję po udanym logowaniu
            //session_start();
            $_SESSION['user_id'] = $UserModel->user_id;
	        $_SESSION['user_password'] = $password; 
            $_SESSION['user_function'] = $UserModel->function;
            $_SESSION['firstName'] = $_POST['firstname'];
            $_SESSION['lastName'] = $_POST['lastname'];
            
            // Tutaj możesz przekierować użytkownika na stronę po zalogowaniu
            header('Location: index.php?action=student_tests');
            exit;
        } else {
            // Błąd logowania, ponowne wyświetlenie formularza z komunikatem
            $errorMessage = 'Nieprawidłowe dane lub hasło.';
            include 'views/login.php';
        }
    }
}

