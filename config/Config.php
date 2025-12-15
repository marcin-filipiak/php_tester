<?php

// Włączanie wyświetlania wszystkich błędów (dla środowiska deweloperskiego)
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');

// Odczyt zmiennych środowiskowych (dla Dockera), fallback na stare wartości (dla lokalnego CLI)
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_user = getenv('DB_USER') ?: 'user';
$db_password = getenv('DB_PASSWORD') ?: 'password';
$db_name = getenv('DB_NAME') ?: 'database_name';

// Definicje (jak wcześniej – z kompatybilności wstecznej)
define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASSWORD', $db_password);
define('DB_NAME', $db_name);
