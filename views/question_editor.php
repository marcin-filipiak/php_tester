<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Edytor Treści</title>
    <style>
    </style>
</head>
<body>

<? include("views/menu.php"); ?>


<div class="editor-container">

<h1><?= $questionData['id'] ? 'Edytuj pytanie' : 'Dodaj pytanie' ?></h1>

<p style="font-size:10px;">
* By stworzyć pytanie otwarte w treści pytania wpisz {?} a następnie podaj jedną poprawną odpowiedź odpowiednio ją punktując.
</p>

<form method="POST" action="">
    <input type="hidden" name="questionId" value="<?= $questionData['id'] ?>">
    <input type="hidden" name="testId" value="<?= $questionData['test_id'] ?>">

    <label>Treść pytania:</label><br>
    <textarea name="content" rows="3" cols="80" id="content"><?= htmlspecialchars($questionData['content']) ?></textarea><br><br>
    
    <button type="submit">Zapisz pytanie</button>
</form>

    <?php if (!empty($questionData['id'])): ?>
        <h4>Odpowiedzi:</h4>
        <?php
            $model = new TestQuestionsModel();
            $answers = $model->getAnswersForQuestion($questionData['id']);
        ?>

        <?php foreach ($answers as $answer): ?>
            <form method="POST" action="index.php?action=test_questions&op=edit_answer">
                <input type="hidden" name="answerId" value="<?= $answer['id'] ?>">
                <input type="hidden" name="questionId" value="<?= $questionData['id'] ?>">
                <input type="hidden" name="testId" value="<?= $questionData['test_id'] ?>">

                <input type="text" name="content" value="<?= htmlspecialchars($answer['content']) ?>" size="60">
                <input type="number" name="points" value="<?= $answer['points'] ?>" style="width: 60px;">
                <button type="submit">Zapisz</button>
                <a href="index.php?action=test_questions&op=delete_answer&answerId=<?= $answer['id'] ?>&testId=<?= $questionData['test_id'] ?>" onclick="return confirm('Usunąć odpowiedź?')">Usuń</a>
            </form>
            <br>
        <?php endforeach; ?>

        <h4>Dodaj nową odpowiedź:</h4>
        <form method="POST" action="index.php?action=test_questions&op=add_answer">
            <input type="hidden" name="questionId" value="<?= $questionData['id'] ?>">
            <input type="hidden" name="testId" value="<?= $questionData['test_id'] ?>">

            <input type="text" name="content" placeholder="Treść odpowiedzi" size="60">
            <input type="number" name="points" placeholder="Punkty" style="width: 60px;">
            <button type="submit">Dodaj</button>
        </form>
    <?php endif; ?>

    <hr>


<a href="index.php?action=test_questions&testId=<?= $questionData['test_id'] ?>">Powrót</a>


</div>

</body>
</html>

