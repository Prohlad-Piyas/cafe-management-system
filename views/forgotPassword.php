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
    <title>Forgot password | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>

<body>
    <section class="brand-panel">
        <h1 class="brand-mark">Chalk<span>&amp; Bean</span></h1>
        <p class="brand-note">
            A reset link works once and stops working after an hour.
        </p>
        <p class="brand-hours">Dhanmondi, Dhaka &nbsp;&middot;&nbsp; Open 8am to 11pm</p>
    </section>

    <section class="form-panel">
        <div class="form-card">
            <h2>Forgot your password</h2>
            <p class="sub">Enter the email on your account and we will send a reset link.</p>

            <?php if(isset($_GET["resetError"])) { ?>
                <p class="notice notice-bad"><?php showGet("resetError"); ?></p>
            <?php } ?>

            <?php if(isset($_GET["requestOk"])) { ?>
                <p class="notice notice-good"><?php showGet("requestOk"); ?></p>
            <?php } ?>

            <?php if(isset($_GET["demoLink"])) { ?>
                <p class="notice notice-good">
                    XAMPP has no mail server, so the link is shown here for the demo:<br>
                    <a href="<?php showGet("demoLink"); ?>">Open reset link</a>
                </p>
            <?php } ?>

            <form action="../controllers/resetControls.php" method="post" onsubmit="return validateForgot()">

                <input type="hidden" name="action" value="request">

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php showGet("email"); ?>"
                           placeholder="you@example.com" autocomplete="email">
                    <span class="err" id="emailErr"><?php showGet("emailError"); ?></span>
                </div>

                <button type="submit" class="btn">Send reset link</button>
            </form>

            <p class="switch">
                Remembered it? <a href="login.php">Back to sign in</a>
            </p>
        </div>
    </section>
</body>

</html>
