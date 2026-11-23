<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>">
    <title>Zmiana hasła</title>
    <style>
    </style>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">
    <h1>Zmiana hasła</h1>
    <form action="index.php?action=account_editor" method="post">
        <div class="form-group">
            <label for="current-password">Obecne hasło:</label>
            <input type="password" id="current-password" name="current-password" required>
        </div>
        <div class="form-group">
            <label for="new-password">Nowe hasło:</label>
            <input type="password" id="new-password" name="new-password" required>
        </div>
        <div class="form-group">
            <label for="confirm-password">Powtórz nowe hasło:</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
        </div>
        <button type="submit" class="login-button">Zmień hasło</button>
    </form>

    <?php if (!empty($message)) : ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!empty($errorMessage)) : ?>
        <div class="alert alert-danger"><?= htmlspecialchars($errorMessage) ?></div>
    <?php endif; ?>
</div>
</body>
</html>

