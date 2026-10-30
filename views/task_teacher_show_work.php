<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Podgląd pracy</title>
    <style>
    </style>
</head>
<body>

    <?php include("views/menu.php"); ?>

<div class="editor-container">

<h2>Praca ucznia: <?= htmlspecialchars($student['firstname']) ?> <?= htmlspecialchars($student['lastname']) ?></h2>

<h3>Załączone pliki:</h3>

<?php if (empty($files)): ?>
    Brak przesłanych plików.
<?php else: ?>
    <?php foreach ($files as $f): ?>
        <h4><?= htmlspecialchars($f['content']) ?></h4>
        <p>
            <a href="index.php?action=tasks&op=download&taskId=<?= (int)$taskId ?>&studentId=<?= (int)$studentId ?>&submissionId=<?= (int)$f['id'] ?>">pobierz plik</a>
        </p>
        <?php
            $ext = strtolower(pathinfo($f['content'], PATHINFO_EXTENSION));
            $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
        ?>
        <?php if ($isImage): ?>
            <img src="index.php?action=tasks&op=viewFile&taskId=<?= (int)$taskId ?>&studentId=<?= (int)$studentId ?>&submissionId=<?= (int)$f['id'] ?>" alt="<?= htmlspecialchars($f['content']) ?>" style="max-width:100%;height:auto;">
        <?php else: ?>
            <textarea rows="30" cols="80" readonly><?= htmlspecialchars(file_get_contents("public/works/$studentId/$taskId/" . $f['content'])) ?></textarea>
        <?php endif; ?>
        
        <form method="POST">
            Ocena: <select name="grade"> 
                        <option value="" >brak</option> 
                    <?php for ($i = 1; $i <= 6; $i++): ?> 
                        <option value="<?= $i ?>" <?= (isset($f['grade']) && $f['grade'] == $i) ? 'selected' : '' ?>><?= $i ?></option> 
                    <?php endfor; ?> 
                    </select> <br><br> 
                            
            Komentarz:<br>
            <textarea name="comment" rows="5" cols="80"><?= htmlspecialchars($f['comment'] ?? '') ?></textarea><br><br>

            <button type="submit">Zapisz</button>
        </form>    
        <hr>
        
    <?php endforeach; ?>
<?php endif; ?>

</div>
</body>
</html>
