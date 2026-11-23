<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>">
    <title>Lista zadań</title>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">

<h1>Lista zadań</h1>

<a class="btn btn-primary btn-sm" href="index.php?action=tasks&op=add">+ Dodaj nowe zadanie</a>

<?php if (!empty($message)): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Tytuł</th>
            <th>Przedmiot</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($tasks)): ?>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['title']) ?></td>
                    <td>
                        <?php
                        if (!empty($task['subiect_id'])) {
                            echo htmlspecialchars($task['subiect_name']);
                        }
                        ?>
                    </td>
<td>
    <a href="index.php?action=tasks&op=edit&taskId=<?= $task['id'] ?>">Edytuj</a> |
    <a href="index.php?action=tasks&op=listWorks&taskId=<?= $task['id'] ?>">Lista prac</a> |
    <a href="index.php?action=tasks&op=delete&taskId=<?= $task['id'] ?>" onclick="return confirm('Na pewno usunąć to zadanie?')">Usuń</a>
</td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">Brak zadań do wyświetlenia.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</div>

</body>
</html>

