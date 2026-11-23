<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>">
    <title>Rejestracja – Tester</title>
</head>
<body class="auth-page">

    <main class="login-container">
        <div class="login-header">
            <img src="assets/gfx/art.jpg" alt="Logo Tester" class="login-logo">
            <h1 class="app-title">Rejestracja</h1>
            <p class="auth-subtitle">Utwórz konto w systemie Tester</p>
        </div>

        <form action="index.php?action=register" method="post">
            <div class="form-group">
                <label for="dailyCode">Kod rejestracji</label>
                <input type="text" id="dailyCode" name="dailyCode" required autocomplete="off">
            </div>

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
                <input type="password" id="password" name="password" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirm-password">Powtórz hasło</label>
                <input type="password" id="confirm-password" name="confirm-password" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="class">Klasa</label>
                <select name="class" id="class" required>
                    <?php foreach ($classes as $cl): ?>
                        <option value="<?= $cl['id_class'] ?>">
                            <?= htmlspecialchars($cl['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <p id="error-message" class="form-error"></p>

            <?php if (isset($errorMessage)) : ?>
                <p class="error-message"><?php echo $errorMessage; ?></p>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary btn-block">Zarejestruj się</button>
        </form>

        <p class="auth-switch">
            Masz już konto? <a href="index.php?action=login">Zaloguj się</a>
        </p>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var password = document.getElementById("password");
            var confirmPassword = document.getElementById("confirm-password");
            var errorDiv = document.getElementById("error-message");

            function checkPasswordMatch() {
                if (confirmPassword.value === "") {
                    errorDiv.textContent = "";
                    return;
                }

                errorDiv.textContent = password.value !== confirmPassword.value
                    ? "Hasła nie są identyczne."
                    : "";
            }

            confirmPassword.addEventListener("input", checkPasswordMatch);
            password.addEventListener("input", checkPasswordMatch);
        });
    </script>

</body>
</html>
