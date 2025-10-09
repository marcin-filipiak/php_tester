<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Edytor Pytań</title>
</head>
<body>

<? include("views/menu.php"); ?>


<div class="editor-container">

<h1>Pytania do testu</h1>


<p>Pytań w teście: <?= count($questions) ?></p> 
📝 <a href="index.php?action=tests&op=edit&testId=<?= $testId ?>">Edytor testu</a><br> 
➕ <a href="index.php?action=test_questions&op=add&testId=<?= $testId ?>">Dodaj pytanie</a>

<hr>

<?php foreach ($questions as $question): ?>
    <div style="margin-bottom: 20px; border-bottom: 1px solid #ccc;">
        <?php
            $content = htmlspecialchars($question['content']);
            $content = preg_replace('/\[image:(.*?)\]/', '<img src="$1" alt="">', $content);
            $content = nl2br($content);
        ?>
    
        <strong><?= $content ?></strong>
        <div>
            <a href="index.php?action=test_questions&op=edit&questionId=<?= $question['id'] ?>&testId=<?= $testId ?>">Edytuj</a> | 
            <a href="index.php?action=test_questions&op=delete&questionId=<?= $question['id'] ?>&testId=<?= $testId ?>" onclick="return confirm('Na pewno usunąć?')">Usuń</a>
        </div>
        <ul>
        <?php foreach ($question['answers'] as $answer): ?>
            <li>
                <?= htmlspecialchars($answer['content']) ?> 
                (<?= $answer['points'] ?> pkt)
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>

</div>

</body>
</html>


