<?php
// Copyright (c) 2025 Marcin Filipiak
// Author: Marcin Filipiak (https://github.com/marcin-filipiak)
// This file is part of TESTER and is licensed under the MIT License.

//czy user jest nauczycielem
function isTeacher() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_function']) && $_SESSION['user_function'] == 1;
}


//ocena z punktów
function gradeFromPoints(int $pointsScored, int $pointsMax) : int {
    // Thresholds (in %) for grades in vocational technical school (example values)
    // Key = minimum % threshold, value = grade in Polish
    $thresholds = [
        90 => 5,
        75 => 4,
        50 => 3,
        30 => 2,
        0  => 1
    ];

    if ($pointsMax <= 0) {
        return 1;
    }

    $percentage = intval(($pointsScored / $pointsMax) * 100);

    foreach ($thresholds as $threshold => $grade) {
        if ($percentage >= $threshold) {
            return $grade;
        }
    }

    return 0;
}


//ocena slowna z numerycznej
function gradeTextFromNumber($numberGrade) : string {

    $thresholds = [
        5 => 'bdb',
        4 => 'db',
        3 => 'dst',
        2 => 'dop',
        1  => 'ndst'
    ];
   
    foreach ($thresholds as $threshold => $textGrade) {
        if (intval($numberGrade) == $threshold) {
            return $textGrade;
        }
    }

    return 'brak oceny';
}

//unikalny kod dzienny do rejestracji
function generateDailyCode($date = null) {
    if ($date === null) {
        $date = date('Y-m-d');  // dzisiejsza data
    }
    $seed = crc32($date); // generujemy seed z daty
    srand($seed); // ustawiamy seed generatora

    $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $length = 5;
    $code = '';

    for ($i = 0; $i < $length; $i++) {
        $index = rand(0, strlen($chars) - 1);
        $code .= $chars[$index];
    }

    srand(); // resetujemy seed, żeby nie wpływało na inne generacje
    return $code;
}


//stylizowanie ocen, ustawienie koloru dla nowych ocen
function styleRecentGrade($date, $gradeText)
{
    // Configuration
    $recentColor = 'green';
    $daysThreshold = 8;

    // Calculate difference in days
    $today = new DateTime();
    $gradeDate = new DateTime($date);
    $difference = $today->diff($gradeDate)->days;

    // Return styled grade if it's recent
    if ($gradeDate <= $today && $difference <= $daysThreshold) {
        return "<span style=\"color: $recentColor; font-weight:bold;\"> ⚠️ $gradeText</span>";
    } else {
        return htmlspecialchars($gradeText);
    }
}


function markdownToHtml($text) {
    // Zamiana podstawowych nagłówków
    $text = preg_replace('/###### (.*)/', '<h6>$1</h6>', $text);
    $text = preg_replace('/##### (.*)/', '<h5>$1</h5>', $text);
    $text = preg_replace('/#### (.*)/', '<h4>$1</h4>', $text);
    $text = preg_replace('/### (.*)/', '<h3>$1</h3>', $text);
    $text = preg_replace('/## (.*)/', '<h2>$1</h2>', $text);
    $text = preg_replace('/# (.*)/', '<h1>$1</h1>', $text);

    // Pogrubienie i kursywa
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);

    // Lista punktowana
    $text = preg_replace_callback('/(?:^|\n)\* (.*?)(?=\n|$)/', function($matches) {
        return '<li>' . $matches[1] . '</li>';
    }, $text);
    if (strpos($text, '<li>') !== false) {
        $text = '<ul>' . $text . '</ul>';
    }

    // Obrazki ![alt](url) -> <img src="url" alt="alt">
    $text = preg_replace_callback('/!\[(.*?)\]\((.*?)\)/', function($matches) {
        $alt = htmlspecialchars($matches[1]);
        $src = htmlspecialchars($matches[2]);
        return '<img src="' . $src . '" alt="' . $alt . '">';
    }, $text);

    // Linki [tekst](url) -> <a href="url" target="_blank">tekst</a>
    $text = preg_replace_callback('/\[(.*?)\]\((.*?)\)/', function($matches) {
        $text = htmlspecialchars($matches[1]);
        $href = htmlspecialchars($matches[2]);
        return '<a href="' . $href . '" target="_blank">' . $text . '</a>';
    }, $text);

    // Zamiana entera na <br>
    $text = nl2br($text);

    return $text;
}


?>

