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

<h1>Lista użytkowników</h1>

<form method="GET" action="">
    <input type="hidden" name="action" value="users">
    <select name="class" onchange="this.form.submit()">
        <option value="">Wszystkie klasy</option>
        <?php foreach ($classes as $cl): ?>
            <option value="<?= $cl['id_class'] ?>" <?= isset($_GET['class']) && $_GET['class'] == $cl['id_class'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cl['name']) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<table>
    <thead>
        <tr>
            <th>Imię</th>
            <th>Nazwisko</th>
            <th>Klasa</th>
            <th>Funkcja</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><?= htmlspecialchars($u['firstname']) ?></td>
            <td><?= htmlspecialchars($u['lastname']) ?></td>
            <td><?= htmlspecialchars($u['class_name'] ?? 'brak') ?></td>
            <td><?= $u['function'] == 1 ? 'Nauczyciel' : 'Uczeń' ?></td>
            <td>
                <a href="index.php?action=users&op=edit&userId=<?= $u['user_id'] ?>">Edytuj</a> |
                <a href="index.php?action=users&op=reset&userId=<?= $u['user_id'] ?>" onclick="return confirm('Na pewno zresetować hasło?')">Reset hasła</a> |
                <a href="index.php?action=users&op=delete&userId=<?= $u['user_id'] ?>" onclick="return confirm('Na pewno usunąć użytkownika?')">Usuń</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</div>
</body>
</html>
