<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Lista klas</title>
    <style>
    </style>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">

<h1>Lista klas</h1>

<a href="index.php?action=classes&op=add">+ Dodaj nową klasę</a>

<table>
    <thead>
        <tr>
            <th>Nazwa</th>
            <th>Opis</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($classList as $class): ?>
            <tr>
                <td><?= htmlspecialchars($class['name']) ?></td>
                <td><?= htmlspecialchars($class['description']) ?></td>
                <td>
                    <a href="index.php?action=classes&op=edit&id=<?= $class['id_class'] ?>">Edytuj</a> |
                    <a href="index.php?action=classes&op=delete&id=<?= $class['id_class'] ?>" onclick="return confirm('Na pewno usunąć?')">Usuń</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>

</body>
</html>

