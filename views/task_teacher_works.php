<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>">
    <title>Lista klas</title>
    <style>
    </style>
</head>
<body>

    <?php include("views/menu.php"); ?>

    <div class="editor-container">

    <h2>Prace dla zadania: <?= htmlspecialchars($task['title']) ?></h2>

    <?php foreach ($classes as $class): ?>
        <h3>Klasa: <?= htmlspecialchars($class['name']) ?></h3>

        <ul>
        
            <?php foreach ($class['students'] as $s): ?>
                <li>
                    <?= htmlspecialchars($s['firstname']) ?> <?= htmlspecialchars($s['lastname']) ?>
                    (<a href="index.php?action=tasks&op=showWork&taskId=<?= $task['id'] ?>&studentId=<?= $s['user_id'] ?>">pokaż pracę</a>)
                </li>
            <?php endforeach; ?>
        </ul>

    <?php endforeach; ?>
</div>

</body>
</html>

