<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

class RegisterController
{
    public function handleRequest()
    {
       // Wysłano dane do rejestracji
	    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $t = $this->processRegisterForm();
		    // Rejestracja prawidłowa
		    if ($t){
		        // Tutaj możesz przekierować użytkownika na stronę po rejestrowaniu
            	    header('Location: index.php?action=login');
	                exit;
		    }
		    // Błąd przy rejestracji
		    else {
		        $errorMessage = "Błędnie wprowadzone dane";
			    $this->displayRegisterPage($errorMessage);
		    }
        }
	    // Jeszcze nie wysłano danych do rejestracji
	    else {
		    $this->displayRegisterPage();
	    }
    }

    // Wyświetlanie strony z form do rejestracji
    private function displayRegisterPage($errorMessage = "")
    {
        $model = new UserModel();
        $classes = $model->getAllClasses();
        // Wyświetl zawartość strony
        include 'views/register.php';
    }

    // Proces rejestracji
    private function processRegisterForm()
    {
	    $firstName = $_POST['firstname'];
	    $lastName = $_POST['lastname'];        
	    $password = $_POST['password'];
        $class = $_POST['class'];
        $dailyCode = $_POST['dailyCode'];
        
        if ($dailyCode == generateDailyCode()){
            $userModel = new UserModel();
            $userModel->register($firstName, $lastName, $password, $class);
            return true;
       }     
       else {
             return false;
       }
    }

}

