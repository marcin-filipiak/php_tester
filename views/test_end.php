<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>">
    <title>Podsumowanie testu</title>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">

    <h1>Podsumowanie testu</h1>

    <div class="result-card">
        <span class="result-label">Twój wynik</span>
        <div class="result-score">
            <?= (int) $result ?> <span>/ <?= (int) $maxpoints ?> pkt</span>
        </div>
        <a class="btn btn-primary" href="index.php?action=student_results">Sprawdź swoje oceny</a>
    </div>

</div>

</body>
</html>
