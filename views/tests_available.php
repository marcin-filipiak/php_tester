<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Testy do wykonania</title>
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>">
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="page-container">
    <h1>Dostępne testy do wykonania</h1>

    <?php if (empty($classId)): ?>
        <p>Nie masz przypisania do klasy.</p>
     <?php endif; ?>
        
    <?php if (empty($tests)): ?>
        <p>Brak dostępnych testów do wykonania.</p>
    <?php else: ?>
        <table>
            <?php foreach ($tests as $test): ?>
                <?php
                      $rowClass = $test['attempts'] > 0 ? 'status-done' : 'status-available';
                ?>
                <tr class="<?= $rowClass; ?>">
                    <td>
                        <strong><?= htmlspecialchars($test['name']) ?></strong><br>
                        <?= nl2br(htmlspecialchars($test['description'])) ?><br><br>
                        Dostępny do: <?= htmlspecialchars($test['test_end']) ?><br>
                        <?= ($test['number_per_student'] > 0) 
                            ? "Pozostało prób: " . ($test['number_per_student'] - $test['attempts']) 
                            : "Nieograniczona liczba podejść" ?><br>
                        Wykonano : <?= $test['attempts'] ?>
                    </td>
                    <td>
                        <?php if ($test['attempts'] == 0): ?>
                            <a class="btn btn-primary btn-sm" href="index.php?action=student_tests&op=start&testId=<?= $test['id'] ?>">Rozpocznij</a>
                        <?php else: ?>
                            <a class="btn btn-secondary btn-sm" href="index.php?action=student_tests&op=start&testId=<?= $test['id'] ?>">Kolejne podejście</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>


