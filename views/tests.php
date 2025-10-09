<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Testy</title>
</head>
<body>

<? include("views/menu.php"); ?>

<?php
// Grupowanie testów po nazwie przedmiotu
$subjects = [];
foreach ($testList as $test) {
    $subjects[$test['subiect_name']][] = $test;
}
?>

<div class="page-container">
    <h1>Testy</h1>

    <a href="index.php?action=tests&op=add">+ Dodaj nowy test</a>

    <?php foreach ($subjects as $subjectName => $tests): ?>
        <h3><?php echo htmlspecialchars($subjectName); ?></h3>
        <table>
            <tr>
                <th>Nazwa</th>
                <th>Akcje</th>
            </tr>
            <?php foreach ($tests as $test): ?>
                <tr>
                    <td><?php echo htmlspecialchars($test['name']); ?></td>
                    <td>
                        <a href="index.php?action=test_questions&testId=<?php echo $test['id']; ?>">Pytania</a>
                        <a href="index.php?action=tests&op=edit&testId=<?php echo $test['id']; ?>">Edycja</a>
                        <a href="index.php?action=tests&op=delete&testId=<?php echo $test['id']; ?>"
                           onclick="return confirm('Na pewno usunąć test z pytaniami, oceny i przypisania do klas?')">
                           Kasuj
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endforeach; ?>
</div>


</body>
</html>

