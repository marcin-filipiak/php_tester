<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Testy do wykonania</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="page-container">
    <h1>Dostępne testy do wykonania</h1>

    <?php if (empty($classId)): ?>
        <p>Nie jesteś przypisany do klasy.</p>
     <?php endif; ?>
        
    <?php if (empty($tests)): ?>
        <p>Brak dostępnych testów do wykonania.</p>
    <?php else: ?>
        <table>
            <?php foreach ($tests as $test): ?>
                <tr>
                    <?php
                          $rowClass = $test['attempts'] > 0 ? 'done' : 'available';
                    ?>
                    <td class="<?= $rowClass; ?>">
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
                            <a href="index.php?action=student_tests&op=start&testId=<?= $test['id'] ?>">Rozpocznij</a>
                        <?php else: ?>
                            <a href="index.php?action=student_tests&op=start&testId=<?= $test['id'] ?>">Kolejne podejście</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

</body>
</html>


