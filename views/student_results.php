<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Twoje wykonane testy</title>
    <link rel="stylesheet" href="assets/css/styles.css" />
</head>
<body>

<?php include("views/menu.php"); ?>

<div class="editor-container">
    <h1>Wykonane testy i punkty</h1>

    <p>
        ⚠️ Nowe oceny zaznaczone są kolorem zielonym i ikoną wykrzyknika.<br>
        Do dziennika wpisywana jest średnia ocen z danego testu.
    </p>


    <?php if (empty($userTests)): ?>
        <p>Nie wykonano jeszcze żadnych testów.</p>
    <?php else: ?>
       
                   <?php foreach ($userTests as $subiectName => $tests): ?>
                <h2><?= htmlspecialchars($subiectName) ?></h2>

                <?php
                    // Grupujemy testy wg nazwy testu
                    $groupedByTestName = [];
                    foreach ($tests as $test) {
                        $groupedByTestName[$test['test_name']][] = $test;
                    }
                ?>

                <table border="1" cellpadding="5" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nazwa testu</th>
                            <th>Data wykonania</th>
                            <th>Wynik</th>
                            <th>Maksymalna liczba punktów</th>
                            <th>Ocena</th>
                            <th>Do dziennika</th>
                        </tr>
                    </thead>
                    <tbody>
    <?php foreach ($groupedByTestName as $testName => $testAttempts): ?>
        <?php
            $sumGrades = 0;
            $countGrades = count($testAttempts);
            foreach ($testAttempts as $attempt) {
                $sumGrades += gradeFromPoints((int)$attempt['result'], (int)$attempt['maxpoints']);
            }
            $avgGrade = round($sumGrades / max($countGrades, 1), 2);
        ?>
        <?php foreach ($testAttempts as $i => $test): ?>
            <tr>
                <td><?= htmlspecialchars($test['test_name']) ?></td>
                <td><?= htmlspecialchars($test['test_date']) ?></td>
                <td><?= (int)$test['result'] ?></td>
                <td><?= (int)$test['maxpoints'] ?></td>
                <td><?= styleRecentGrade($test['test_date'], gradeFromPoints((int)$test['result'], (int)$test['maxpoints'])) ?></td>
                
                <?php if ($i === 0): ?>
                    <td rowspan="<?= $countGrades ?>"><?= $avgGrade ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</tbody>
                </table>
            <?php endforeach; ?>
       
    <?php endif; ?>
</div>

</body>
</html>

