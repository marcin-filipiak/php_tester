<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>">
    <title>Logowanie – Tester</title>
</head>
<body class="auth-page">

    <main class="login-container">
        <div class="login-header">
            <img src="assets/gfx/art.jpg" alt="Logo Tester" class="login-logo">
            <h1 class="app-title">Tester</h1>
            <p class="auth-subtitle">Szkolny system testów i zadań</p>
        </div>

        <form action="index.php?action=login" method="post">
            <div class="form-group">
                <label for="firstname">Imię</label>
                <input type="text" id="firstname" name="firstname" required autocomplete="given-name">
            </div>
            <div class="form-group">
                <label for="lastname">Nazwisko</label>
                <input type="text" id="lastname" name="lastname" required autocomplete="family-name">
            </div>
            <div class="form-group">
                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>

            <?php if (isset($errorMessage)) : ?>
                <p id="error-message" class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary btn-block">Zaloguj się</button>
        </form>

        <p class="auth-switch">
            Nie masz konta? <a href="index.php?action=register">Zarejestruj się</a>
        </p>

        <div class="auth-footer">
            <a href="https://tester.filipiak.cc/tester.apk" target="_blank" rel="noopener">Aplikacja dla Androida</a>
            <span>&copy; <a href="https://www.facebook.com/marcin.filipiak" target="_blank" rel="noopener">Marcin Filipiak</a></span>
        </div>
    </main>

    <div class="site-credit">
        <a href="https://mikr.us/?r=36b1bce5" target="_blank" rel="noopener">
            <img src="https://mikr.us/logotypy/mikrus_maly.png" alt="serwery dla pasjonatów">
        </a>
    </div>

</body>
</html>
