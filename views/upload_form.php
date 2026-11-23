<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pliki – Tester</title>
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>">
</head>
<body>
<?php include("views/menu.php"); ?>

<div class="editor-container">

    <h1>Pliki</h1>

    <div class="alert alert-warning">
        Przesyłanie plików przez tę zakładkę jest czasowo wyłączone.
    </div>

    <p>
        Pliki z zadaniami odeślij w zakładce
        <a href="index.php?action=tasks">Zadania</a>.
    </p>

</div>
</body>
</html>
