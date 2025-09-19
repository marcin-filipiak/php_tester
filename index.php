<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

// Importowanie konfiguracji
include('config/Config.php');

session_start();

// Pomocnicze funkcje
include('includes/helpers.php');


// Importowanie klasy Database
include('includes/Database.php');

// Autoloader do automatycznego ładowania klas
spl_autoload_register(function ($class) {
    @include 'controllers/' . $class . '.php';
    @include 'models/' . $class . '.php';
});

// Pobierz żądanie użytkownika
$action = isset($_GET['action']) ? $_GET['action'] : 'login';

// Kontroler główny
switch ($action) {
    case 'register':
        $controller = new RegisterController();
        break;
    case 'login':
        $controller = new LoginController();
        break;
     case 'account_editor':
        $controller = new AccountEditorController();
        break;
    case 'subjects':
        $controller = new SubjectsController();
    break;    
    case 'classes':
        $controller = new ClassesController();
    break;
    case 'users':
        $controller = new UserController();
    break;
	case 'tests':
	    $controller = new TestsController();
	break;
    case 'test_questions':
	    $controller = new TestQuestionsController();
	break;
	case 'student_tests':
        $controller = new StudentTestsController();
    break;
    case 'student_results':
        $controller = new StudentResultsController();
    break;
    case 'class_results':
        $controller = new ClassResultsController();
    break;
    case 'importexport':
        $controller = new ImportExportController();
    break;
    case 'logout':
        $controller = new LogoutController();
        break;
    default:
        // Przekierowanie na stronę logowania w przypadku nieznanego żądania
        header('Location: index.php?action=login');
        exit;
}

// Wywołaj odpowiednią metodę kontrolera
$controller->handleRequest();

?>
