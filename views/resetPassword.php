<?php
require_once __DIR__."/../controllers/authGuard.php";
require_once __DIR__."/../models/userModel.php";

if(isLoggedIn())
    {
        header("Location: ".dashboardFor($_SESSION["role"]));
        exit;
    }

$token=$_GET["token"]??"";
if(findUserIdByToken($token)==null)
    {
        header("Location: forgotPassword.php?resetError=".urlencode("This reset link is invalid or has expired. Request a new one."));
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Choose a new password | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>

<body>
    <section class="brand-panel">
        <h1 class="brand-mark">Chalk<span>&amp; Bean</span></h1>
        <p class="brand-note">
            After this the link is used up and cannot be opened again.
        </p>
        <p class="brand-hours">Dhanmondi, Dhaka &nbsp;&middot;&nbsp; Open 8am to 11pm</p>
    </section>

    <section class="form-panel">
        <div class="form-card">
            <h2>Choose a new password</h2>
            <p class="sub">At least 8 characters, with a capital, a small letter and a number.</p>

            <?php if(isset($_GET["resetError"])) { ?>
                <p class="notice notice-bad"><?php showGet("resetError"); ?></p>
            <?php } ?>

            <form action="../controllers/resetControls.php" method="post" onsubmit="return validateReset()">

                <input type="hidden" name="action" value="reset">
                <input type="hidden" name="token" value="<?php echo e($token); ?>">

                <div class="field">
                    <label for="newPass">New password</label>
                    <input type="password" id="newPass" name="newPass"
                           placeholder="At least 8 characters" autocomplete="new-password">
                    <span class="err" id="newPassErr"><?php showGet("newPassError"); ?></span>
                </div>

                <div class="field">
                    <label for="conPass">Confirm new password</label>
                    <input type="password" id="conPass" name="conPass"
                           placeholder="Type it again" autocomplete="new-password">
                    <span class="err" id="conPassErr"><?php showGet("conPassError"); ?></span>
                </div>

                <button type="submit" class="btn">Save new password</button>
            </form>

            <p class="switch">
                <a href="login.php">Back to sign in</a>
            </p>
        </div>
    </section>
</body>

</html>
