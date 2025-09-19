<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Lista przedmiotów</title>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">

<h1>Lista przedmiotów</h1>

<a href="index.php?action=subjects&op=add">+ Dodaj nowy przedmiot</a>

<table>
    <thead>
        <tr>
            <th>Nazwa</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($subjectList as $subject): ?>
            <tr>
                <td><?= htmlspecialchars($subject['name']) ?></td>
                <td>
                    <a href="index.php?action=subjects&op=edit&id=<?= $subject['id'] ?>">Edytuj</a> |
                    <a href="index.php?action=subjects&op=delete&id=<?= $subject['id'] ?>" onclick="return confirm('Na pewno usunąć?')">Usuń</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>

</body>
</html>

