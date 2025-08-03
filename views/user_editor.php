<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Wyświetlanie Strony</title>
</head>
<body>

<?php include('views/menu.php'); ?>

<div class="editor-container">

<h2>Edytuj użytkownika</h2>

<form method="POST">
    <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
    
    <label>Imię: <input type="text" name="firstname" value="<?= htmlspecialchars($user['firstname']) ?>"></label><br>
    <label>Nazwisko: <input type="text" name="lastname" value="<?= htmlspecialchars($user['lastname']) ?>"></label><br>

    <label>Klasa:
         <select name="class">
        <option value="" <?= ($user['class'] === null || $user['class'] === '') ? 'selected' : '' ?>>brak</option>
        <?php foreach ($classes as $cl): ?>
            <option value="<?= $cl['id_class'] ?>" <?= $user['class'] == $cl['id_class'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cl['name']) ?>
            </option>
        <?php endforeach;?>
        </select>
    </label><br><br>
    
        <label>Funkcja:
        <select name="function">
            <option value="0" <?= $user['function'] == 0 ? 'selected' : '' ?>>Uczeń</option>
            <option value="1" <?= $user['function'] == 1 ? 'selected' : '' ?>>Nauczyciel</option>
        </select>
    </label><br><br>

    <button type="submit">Zapisz</button>
</form>

</div>
</body>
</html>
