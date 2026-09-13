<?php
require_once __DIR__."/../controllers/authGuard.php";

if(isLoggedIn())
    {
        header("Location: ".dashboardFor($_SESSION["role"]));
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>

<body>
    <section class="brand-panel">
        <h1 class="brand-mark">Chalk<span>&amp; Bean</span></h1>
        <p class="brand-note">
            Orders, staff and payments for the counter, the kitchen and the floor.
        </p>
        <p class="brand-hours">Dhanmondi, Dhaka &nbsp;&middot;&nbsp; Open 8am to 11pm</p>
    </section>

    <section class="form-panel">
        <div class="form-card">
            <h2>Sign in</h2>
            <p class="sub">Use the email address on your caf&eacute; account.</p>

            <?php if(isset($_GET["registerOk"])) { ?>
                <p class="notice notice-good"><?php showGet("registerOk"); ?></p>
            <?php } ?>

            <?php if(isset($_GET["loginError"])) { ?>
                <p class="notice notice-bad"><?php showGet("loginError"); ?></p>
            <?php } ?>

            <form action="../controllers/loginControls.php" method="post" onsubmit="return validateLogin()">

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php showGet("email"); ?>"
                           placeholder="you@example.com" autocomplete="email">
                    <span class="err" id="emailErr"><?php showGet("emailError"); ?></span>
                </div>

                <div class="field">
                    <label for="pass">Password</label>
                    <input type="password" id="pass" name="pass"
                           placeholder="Your password" autocomplete="current-password">
                    <span class="err" id="passErr"><?php showGet("passError"); ?></span>
                </div>

                <button type="submit" class="btn">Sign in</button>
            </form>

            <p class="switch">
                <a href="forgotPassword.php">Forgot your password?</a>
                &nbsp;&middot;&nbsp;
                New customer? <a href="register.php">Create an account</a>
            </p>
        </div>
    </section>
</body>

</html>
