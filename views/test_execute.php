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
        <?php
            $isInput = strpos($q['content'], '{?}') !== false;
            $answers = (new TestExecutionModel())->getAnswersByQuestion($q['id']);

            $content = htmlspecialchars($q['content']);

            if ($isInput) {
                $input = '<input type="text" name="question_' . (int)$q['id'] . '" style="width: 100px">';
                $content = str_replace('{?}', $input, $content);
            }

            $content = preg_replace(
                '/\[image:(.*?)\]/',
                '<img src="$1" alt="" width="400px">',
                $content
            );

            $content = nl2br($content);
        ?>

        <p><strong><?= $content ?></strong></p>

        <?php if (!$isInput): ?>
            <table>
                <?php foreach ($answers as $a): ?>
                    <tr>
                        <td>
                            <input type="radio"
                                   name="question_<?= (int)$q['id'] ?>"
                                   value="<?= (int)$a['id'] ?>">
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

