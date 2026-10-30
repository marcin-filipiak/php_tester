<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Zadania ucznia</title>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">

<h1>Zadania dla Twojej klasy</h1>

<table>
    <thead>
        <tr>
            <th>Tytuł</th>
            <th>Przedmiot</th>
            <th>Treść</th>
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
                        <a href="index.php?action=tasks&op=view&taskId=<?= $task['id'] ?>">Przeczytaj</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3">Brak zadań do wyświetlenia dla Twojej klasy.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</div>

</body>
</html>

