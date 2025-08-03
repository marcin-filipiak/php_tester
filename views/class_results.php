<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Wyniki klasy</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">
    <h1>Wyniki testów w klasie</h1>
    
    <p>⚠️ Nowe oceny zaznaczone są kolorem zielonym i ikoną wykrzyknika.</p>

    <form method="GET" action="">
        <input type="hidden" name="action" value="class_results">
        <label for="classId">Wybierz klasę:</label>
        <select name="classId" id="classId">
            <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id_class'] ?>" <?= ($c['id_class'] == $selectedClass) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Pokaż</button>
    </form>

    <?php if ($resultsData): ?>
        <table>
            <thead>
                <tr>
                        <th>Uczeń</th>
                    <?php foreach ($resultsData['tests'] as $testName): ?>
                        <th><?= htmlspecialchars($testName) ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultsData['results'] as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <?php foreach ($resultsData['tests'] as $testName): ?>
                            <td>
                                <?php if (!empty($user['results'][$testName])): ?>
                                <?php
                                    $sumGrades = 0;
                                    $countGrades = 0;
                                ?>
                                    <?php foreach ($user['results'][$testName] as $entry): ?>
                                        
                                        <?php
                                            $grade = gradeFromPoints((int)$entry['result'], (int)$entry['maxpoints']);
                                            $sumGrades += $grade;
                                            $countGrades++;
                                        ?>
                                        
                                        <?= styleRecentGrade($entry['date'], htmlspecialchars($entry['grade'])) ?>
                                        <?= htmlspecialchars($entry['result']) ?>/<?= htmlspecialchars($entry['maxpoints']) ?>
                                        [<?= htmlspecialchars($entry['date']) ?>]
                                        <a class="delete-link" 
                                           href="?action=class_results&classId=<?= urlencode($selectedClass) ?>&delete=<?= $entry['id'] ?>"
                                           onclick="return confirm('Czy na pewno chcesz usunąć tę ocenę?')">🗑️</a><br>
                                    <?php endforeach; ?>
                                    
                                    <b>Dziennik: <?=  gradeTextFromNumber(round($sumGrades / max($countGrades, 1), 2)) ?></b>
                                    
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>

