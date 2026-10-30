<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Prześlij plik</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<?php include("views/menu.php"); ?>

<div class="editor-container">

<!---
    <h1>Prześlij plik z pracą</h1>

    <?php if (!empty($messages['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($messages['error']) ?>
        </div>
    <?php elseif (!empty($messages['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($messages['success']) ?>
        </div>
    <?php endif; ?>


    <form method="post" action="index.php?action=upload" enctype="multipart/form-data">
        <p>
            <label>Wybierz plik (maks. <?= number_format(MAX_UPLOAD_SIZE / (1024 * 1024), 1) ?> MB):</label><br>
            <input type="file" name="uploaded_file" required>
        </p>

        <p>
            <button type="submit">Wyślij plik</button>
        </p>
    </form>
    --->
    <h1> Funkcja czasowo wyłączona</h1>
    Pliki z zadaniami odeślij w zakładce zadania
</div>
</body>
</html>
