<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/styles.css?v=<?= time() ?>"">
    <title>Logowanie do Testera</title>
</head>
<body>

    <div class="login-container">
    
        <div class="login-header">
            <img src="assets/gfx/art.jpg" alt="Logo" class="login-logo">
            <h1 class="app-title">Tester</h1>
            <span style="font-size:9px;">&copy; <a href="https://www.facebook.com/marcin.filipiak" target="_blank">Marcin Filipiak</a></span>
        </div>

        <form action="index.php?action=login" method="post">
            <div class="form-group">
                <label for="firstname">imie:</label>
                <input type="text" id="firstname" name="firstname" required autocomplete="given-name">
            </div>
            <div class="form-group">
                <label for="lastname">nazwisko:</label>
                <input type="text" id="lastname" name="lastname" required autocomplete="family-name">
            </div>
            <div class="form-group">
                <label for="password">hasło:</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="login-button">Zaloguj</button>
            <a class="login-button" href="index.php?action=register">Rejestruj</a>
        </form>
        
        <hr>
        <a href="https://tester.filipiak.cc/tester.apk" target="_blank">Aplikacja dla Androida</a>
        
        <?php if (isset($errorMessage)) : ?>
            <p id="error-message" class="error-message"><?php echo $errorMessage; ?></p>
        <?php endif; ?>
    </div>

<div style="margin-top:50px;text-align:center;"> 
<a href="https://mikr.us/?r=36b1bce5" target="_blank">
<img src="https://mikr.us/logotypy/mikrus_maly.png" alt="serwery dla pasjonatów">
</a>
</div>

</body>
</html>

