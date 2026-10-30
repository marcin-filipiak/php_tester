<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Podgląd zadania</title>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">

    <h1><?= htmlspecialchars($task['title']) ?></h1>

    <p><a href="index.php?action=tasks">← Powrót do listy zadań</a></p>


    <div style="border:1px solid #ccc; padding:10px; margin-bottom:15px;">
        <h3>Twoje załączniki</h3>

        <?php if (!empty($files)): ?>
            <ul>
                <?php foreach ($files as $file): ?>
                    <li>
                        <?php echo htmlspecialchars($file['content']); ?>
                        
                        <a href="index.php?action=tasks&op=download&taskId=<?php echo $taskId; ?>&submissionId=<?php echo $file['id']; ?>">pobierz</a>
                        
                        <a href="index.php?action=tasks&op=view&taskId=<?php echo $taskId; ?>&deleteFile=<?php echo $file['id']; ?>"
                           onclick="return confirm('Usunąć plik?');"
                           style="color:red; margin-left:10px;">
                            usuń
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Brak przesłanych plików.</p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" style="margin-top:10px;">
            <input type="file" name="file" required>
            <button type="submit">Wyślij plik z pracą</button>
        </form>
    </div>

    <p><strong>Treść zadania:</strong></p>
    <div class="task-content">
        <?= markdownToHtml(($task['content'])) ?>
    </div>


</div>

</body>
</html>

