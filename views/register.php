<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css">
    <title>Rejestruj</title>
    <style>
    </style>
</head>
<body>

    <div class="login-container">
        <h2>Rejestruj</h2>
        <form action="index.php?action=register" method="post">
                    
            <div class="form-group">
                <label for="dailyCode">kod rejestracji:</label>
                <input type="text" id="dailyCode" name="dailyCode" required>
            </div>
        
            <div class="form-group">
                <label for="firstname">imie:</label>
                <input type="text" id="firstname" name="firstname" required>
            </div>
            <div class="form-group">
                <label for="lastname">nazwisko:</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>
            <div class="form-group">
                <label for="password">hasło:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">ponownie hasło:</label>
                <input type="password" id="confirm-password" name="confirm-password" required>
            </div>
            <div class="form-group">
                <label for="class">klasa:</label>
                <select name="class" required>
                    <?php foreach ($classes as $cl): ?>
                        <option value="<?= $cl['id_class'] ?>" >
                            <?= htmlspecialchars($cl['name']) ?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
	    <p id="error-message" style="color:red;"></p>
            <button type="submit" class="login-button">Rejestruj</button>

        </form>
        <?php if (isset($errorMessage)) : ?>
            <p class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
    </div>


<script>
        document.addEventListener("DOMContentLoaded", function() {
            var password = document.getElementById("password");
            var confirmPassword = document.getElementById("confirm-password");
            var errorDiv = document.getElementById("error-message");

            function checkPasswordMatch() {
                var passwordValue = password.value;
                var confirmPasswordValue = confirmPassword.value;

                if (passwordValue !== confirmPasswordValue) {
                    errorDiv.textContent = "Hasła nie są identyczne.";
                } else {
                    errorDiv.textContent = "";
                }
            }

            confirmPassword.addEventListener("input", checkPasswordMatch);
	    password.addEventListener("input", checkPasswordMatch);
        });
</script>

</body>
</html>

