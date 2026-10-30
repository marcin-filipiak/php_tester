<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Edytor Zadania</title>
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">
    <h1>Edytor Zadania</h1>

<form method="POST" action="">

    <input type="hidden" name="taskId" value="<?= htmlspecialchars($taskData['id']) ?>">


    <label>
        Tytuł zadania:
        <input type="text" name="title" value="<?= htmlspecialchars($taskData['title']) ?>" required>
    </label>

    <label>
        Treść zadania:
        <textarea name="content" required style="height:400px;"><?= htmlspecialchars($taskData['content']) ?></textarea>
    </label>

    <label>
        Przedmiot:
        <select name="subiect_id">
            <?php foreach ($subiects as $sub): ?>
                <option value="<?= $sub['id'] ?>" <?= ($sub['id'] == $taskData['subiect_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sub['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <h3>Przypisane klasy</h3>
    <table>
        <tr><th>Klasa</th><th>Termin</th><th>Usuń</th></tr>
        <?php foreach ($assignedClasses as $assignment): ?>
            <tr>
                <td><?= htmlspecialchars($assignment['name']) ?></td>
                <td>
                    <input type="date" name="assigned_classes_dates[<?= $assignment['class_id'] ?>]" value="<?= htmlspecialchars($assignment['due_date'] ?? '') ?>">
                </td>
                <td>
                    <input type="checkbox" name="remove_classes[]" value="<?= $assignment['class_id'] ?>">
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <button type="submit">Zapisz zmiany</button>
</form>

<hr>

<h4>Dodaj nowe przypisanie klasy</h4>
<form method="POST" action="index.php?action=tasks&op=saveAssignment">
    <label>
        Wybierz klasę:
        <select name="class_id" required>
            <option value="">-- wybierz klasę --</option>
            <?php foreach ($classes as $class): ?>
                <?php if (!in_array($class['id_class'], array_column($assignedClasses, 'class_id'))): ?>
                    <option value="<?= $class['id_class'] ?>">
                        <?= htmlspecialchars($class['name']) ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Termin:
        <input type="date" name="assignment_date">
    </label>

    <input type="hidden" name="task_id" value="<?= $taskData['id'] ?>">

    <button type="submit">Dodaj przypisanie</button>
</form>


</div>

</body>
</html>

