<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Edytor Treści</title>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">
    <h1>Edytor Testu</h1>

<form method="POST" action="">
    <input type="hidden" name="testId" value="<?= $testData['id'] ?>">
    <!-- pola edycji testu -->
    <input type="text" name="name" value="<?= htmlspecialchars($testData['name']) ?>" placeholder="Nazwa testu">
    <textarea name="description"><?= htmlspecialchars($testData['description']) ?></textarea>

    <table>
        <tr>
        <td>Opublikowany dla nauczycieli</td> <td><input type="checkbox" name="published" <?= $testData['published'] ? 'checked' : '' ?>></td>
        </tr>
        <tr>
        <td>Udostępniony dla uczniów</td> <td><input type="checkbox" name="shared" <?= $testData['shared'] ? 'checked' : '' ?>></td>
        </tr>
    </table>
    <label>
        Losuj pytań <input type="number" name="questionrand" value="<?= $testData['questionrand'] ?>" >
    </label>
    <label>
        Podejść <input type="number" name="number_per_student" value="<?= $testData['number_per_student'] ?>" >
    </label>
    <label>
        Przedmiot
        <select name="id_subiect">
            <?php foreach ($subiects as $sub): ?>
                <option value="<?= $sub['id'] ?>" <?= ($sub['id'] == $testData['id_subiect']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sub['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>


    <h3>Przypisane klasy</h3>
    <table>
        <tr><th>Klasa</th><th>Termin</th><th>Usuń</th></tr>
        <?php foreach ($allClassAssignments as $assignment): ?>
            <tr>
                <td><?= htmlspecialchars($assignment['class_name']) ?></td>
                <td>
                    <input type="date" name="class_dates[<?= $assignment['class_id'] ?>]" value="<?= htmlspecialchars($assignment['test_end']) ?>">
                </td>
                <td>
                    <input type="checkbox" name="remove_class_ids[]" value="<?= $assignment['class_id'] ?>">
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <button type="submit">Zapisz zmiany</button>
</form>

<hr>

<!-- OSOBNY FORMULARZ DO DODAWANIA NOWEGO PRZYPISANIA -->
<h4>Dodaj nowe przypisanie</h4>
<form method="post" action="index.php?action=tests&op=saveAssignment">
    
    <div class="form-group">
        <label for="class_id">Wybierz klasę:</label>
        <select name="class_id" id="class_id" required>
            <option value="">-- wybierz klasę --</option>
            <?php foreach ($classes as $class): ?>
                <?php if (!in_array($class['id_class'], array_column($allClassAssignments, 'class_id'))): ?>
                    <option value="<?= $class['id_class'] ?>">
                        <?= htmlspecialchars($class['name']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="assignment_date">Data:</label>
        <input type="date" name="assignment_date" id="assignment_date" required>
    </div>

    <input type="hidden" name="test_id" value="<?= $testData['id'] ?>">

    <button type="submit">Dodaj przypisanie</button>
</form>

</div>

</body>
</html>

