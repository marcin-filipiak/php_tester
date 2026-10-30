<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Edytor przedmiotu</title>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">

<h1><?= $subjectData['id'] ? 'Edytuj przedmiot' : 'Dodaj przedmiot' ?></h1>

<form method="post" action="index.php?action=subjects&op=<?= $subjectData['id'] ? 'edit' : 'add' ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($subjectData['id']) ?>">

    <label for="name">Nazwa:</label><br>
    <input type="text" id="name" name="name" value="<?= htmlspecialchars($subjectData['name']) ?>" required><br><br>

    <button type="submit">Zapisz</button>
    <a href="index.php?action=subjects">Anuluj</a>
</form>

</div>

</body>
</html>

