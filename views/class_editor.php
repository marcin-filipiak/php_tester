<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Edytor klasy</title>
    <style>
    </style>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">
<h1>Edytor klasy</h1>

<form method="POST" action="">
    <input type="hidden" name="id_class" value="<?= $classData['id_class'] ?>">

    <label>Nazwa:
        <input type="text" name="name" value="<?= htmlspecialchars($classData['name']) ?>" required>
    </label>

    <label>Opis:
        <textarea name="description"><?= htmlspecialchars($classData['description']) ?></textarea>
    </label>

    <button type="submit">Zapisz</button>
</form>
</div>

</body>
</html>

