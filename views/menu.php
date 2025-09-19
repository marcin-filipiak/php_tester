
<div id="menu">

    <a><?=$_SESSION['firstName']?> <?=$_SESSION['lastName']?></a>

    <?php if (isTeacher()): ?>
        <a href="index.php?action=classes">Klasy</a>
        <a href="index.php?action=subjects">Przedmioty</a>
        <a href="index.php?action=users">Użytkownicy</a>
        <a href="index.php?action=tests">Testy</a>
        <a href="index.php?action=importexport">I/O</a>
        <a href="index.php?action=class_results">Oceny Klasy</a>
    <?php endif; ?>

    <a href="index.php?action=student_tests">Dostępne testy</a>
    <a href="index.php?action=student_results">Twoje Oceny</a>
    <a href="index.php?action=account_editor">Edytuj konto</a>
    <a href="index.php?action=logout">Wyloguj</a>
    
    <?php if (isTeacher()): ?>
        <span>kod rejestracji: <?= generateDailyCode()?></span>
    <?php endif; ?>
</div>
