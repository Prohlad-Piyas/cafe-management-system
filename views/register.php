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
    <title>Create an account | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>

<body>
    <section class="brand-panel">
        <h1 class="brand-mark">Chalk<span>&amp; Bean</span></h1>
        <p class="brand-note">
            Make an account to order from the counter and follow your food to the table.
        </p>
        <p class="brand-hours">Dhanmondi, Dhaka &nbsp;&middot;&nbsp; Open 8am to 11pm</p>
    </section>

    <section class="form-panel">
        <div class="form-card">
            <h2>Create an account</h2>
            <p class="sub">Customer accounts only. Staff logins are made by the manager.</p>

            <?php if(isset($_GET["registerError"])) { ?>
                <p class="notice notice-bad"><?php showGet("registerError"); ?></p>
            <?php } ?>

            <form action="../controllers/registerControls.php" method="post" onsubmit="return validateRegister()">

                <div class="field">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" value="<?php showGet("name"); ?>"
                           placeholder="Sadim Fahad" autocomplete="name">
                    <span class="err" id="nameErr"><?php showGet("nameError"); ?></span>
                </div>

                <div class="field">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?php showGet("phone"); ?>"
                           placeholder="01712345678" autocomplete="tel">
                    <span class="err" id="phoneErr"><?php showGet("phoneError"); ?></span>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php showGet("email"); ?>"
                           placeholder="you@example.com" autocomplete="email">
                    <span class="err" id="emailErr"><?php showGet("emailError"); ?></span>
                </div>

                <div class="field">
                    <label for="pass">Password</label>
                    <input type="password" id="pass" name="pass"
                           placeholder="At least 8 characters" autocomplete="new-password">
                    <span class="err" id="passErr"><?php showGet("passError"); ?></span>
                </div>

                <div class="field">
                    <label for="conPass">Confirm password</label>
                    <input type="password" id="conPass" name="conPass"
                           placeholder="Type it again" autocomplete="new-password">
                    <span class="err" id="conPassErr"><?php showGet("conPassError"); ?></span>
                </div>

                <button type="submit" class="btn">Create account</button>
            </form>

            <p class="switch">
                Already have an account? <a href="login.php">Sign in</a>
            </p>
        </div>
    </section>
</body>

</html>
