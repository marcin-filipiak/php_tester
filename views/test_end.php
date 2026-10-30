<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Podsumowanie testu</title>
    <style>
    </style>
</head>
<body>

<? include("views/menu.php"); ?>


<div class="editor-container">

<h1>Podsumowanie testu</h1>

   <p> Twój wynik <?= $result;?> na <?= $maxpoints;?> punkty </p><br>
   <a href="index.php?action=student_results">Sprawdź swoje oceny</a>

</div>

</body>
</html>


