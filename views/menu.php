<?php
// Aktywna pozycja menu na podstawie bieżącej akcji
$currentAction = $_GET['action'] ?? '';
$navState = static function (array $actions) use ($currentAction): string {
    return in_array($currentAction, $actions, true)
        ? ' class="active" aria-current="page"'
        : '';
};

$fullName = trim(($_SESSION['firstName'] ?? '') . ' ' . ($_SESSION['lastName'] ?? ''));
$homeAction = isTeacher() ? 'classes' : 'student_tests';
?>

<header class="site-header">
    <div class="header-inner">
        <div class="header-top">
            <a class="brand" href="index.php?action=<?= $homeAction ?>" aria-label="Tester – strona główna">
                <img class="brand-logo" src="assets/gfx/art.jpg" alt="">
                <span class="brand-name">Tester</span>
            </a>

            <div class="nav-account">
                <?php if ($fullName !== ''): ?>
                    <span class="nav-user"><?= htmlspecialchars($fullName) ?></span>
                <?php endif; ?>

                <?php if (isTeacher()): ?>
                    <span class="nav-code" title="Kod rejestracji na dziś">
                        Kod: <strong><?= htmlspecialchars(generateDailyCode()) ?></strong>
                    </span>
                <?php endif; ?>

                <a class="nav-logout" href="index.php?action=logout">Wyloguj</a>
            </div>

            <button class="nav-toggle" type="button" aria-label="Otwórz menu"
                    aria-expanded="false" aria-controls="mainNav">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <nav class="main-nav" id="mainNav" aria-label="Nawigacja główna">
            <div class="nav-links">
                <?php if (isTeacher()): ?>
                    <a href="index.php?action=classes"<?= $navState(['classes']) ?>>Klasy</a>
                    <a href="index.php?action=subjects"<?= $navState(['subjects']) ?>>Przedmioty</a>
                    <a href="index.php?action=users"<?= $navState(['users']) ?>>Użytkownicy</a>
                    <a href="index.php?action=tests"<?= $navState(['tests', 'test_questions']) ?>>Testy</a>
                    <a href="index.php?action=importexport"<?= $navState(['importexport']) ?>>Import / Eksport</a>
                    <a href="index.php?action=class_results"<?= $navState(['class_results']) ?>>Wyniki klas</a>
                    <span class="nav-divider" aria-hidden="true"></span>
                <?php endif; ?>

                <a href="index.php?action=student_tests"<?= $navState(['student_tests']) ?>>Dostępne testy</a>
                <a href="index.php?action=student_results"<?= $navState(['student_results']) ?>>Twoje oceny</a>
                <a href="index.php?action=tasks"<?= $navState(['tasks']) ?>>Zadania</a>
                <a href="index.php?action=upload"<?= $navState(['upload']) ?>>Pliki</a>
                <a href="index.php?action=account_editor"<?= $navState(['account_editor']) ?>>Edytuj konto</a>
            </div>
        </nav>
    </div>
</header>

<script>
(function () {
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.getElementById('mainNav');

    if (toggle && nav) {
        toggle.addEventListener('click', function () {
            var isOpen = nav.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            toggle.setAttribute('aria-label', isOpen ? 'Zamknij menu' : 'Otwórz menu');
        });

        nav.addEventListener('click', function (event) {
            if (event.target.tagName === 'A' && window.matchMedia('(max-width: 960px)').matches) {
                nav.classList.remove('is-open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Otwórz menu');
            }
        });
    }

    // Tabele zyskują własny poziomy scroll, dzięki czemu nie rozpychają układu
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('table').forEach(function (table) {
            if (table.parentElement && table.parentElement.classList.contains('table-wrap')) {
                return;
            }
            var wrapper = document.createElement('div');
            wrapper.className = 'table-wrap';
            table.parentNode.insertBefore(wrapper, table);
            wrapper.appendChild(table);
        });
    });
})();
</script>
