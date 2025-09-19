<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Rozwiązywanie testu</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="page-container">
    <h1><?= htmlspecialchars($test['name']) ?></h1>
    <p><?= htmlspecialchars($test['description']) ?></p>
    <hr>
    <form method="POST" action="index.php?action=student_tests&op=submit">
        <input type="hidden" name="testId" value="<?= $test['id'] ?>">
        
<?php foreach ($questions as $q): ?>
    <div class="question-block">
        <p><strong><?//= nl2br(htmlspecialchars($q['content'])) ?><?= $q['content'] ?></strong></p>

        <?php
            $isInput = strpos($q['content'], '{?}') !== false;
            $answers = (new TestExecutionModel())->getAnswersByQuestion($q['id']);
        ?>

        <?php if ($isInput): ?>
            <input type="text" name="question_<?= $q['id'] ?>" style="width: 100%;">
        <?php else: ?>
            <table>
                <?php foreach ($answers as $a): ?>
                    <tr>
                        <td>
                            <input type="radio" name="question_<?= $q['id'] ?>" value="<?= $a['id'] ?>">
                        </td>
                        <td><?= nl2br(htmlspecialchars($a['content'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
    <hr>
<?php endforeach; ?>

        <button type="submit">Zakończ i wyślij</button>
    </form>
</div>

</body>
</html>

