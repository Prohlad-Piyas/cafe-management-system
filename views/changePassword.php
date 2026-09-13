<?php
require_once __DIR__."/../controllers/authGuard.php";
if(!isLoggedIn())
    {
        header("Location: login.php");
        exit;
    }

$backLink=dashboardFor($_SESSION["role"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change password | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>

<body>
    <section class="brand-panel">
        <h1 class="brand-mark">Chalk<span>&amp; Bean</span></h1>
        <p class="brand-note">
            Signed in as <?php echo e($_SESSION["name"]); ?> (<?php echo e($_SESSION["role"]); ?>).
        </p>
        <p class="brand-hours">Dhanmondi, Dhaka &nbsp;&middot;&nbsp; Open 8am to 11pm</p>
    </section>

    <section class="form-panel">
        <div class="form-card">
            <h2>Change password</h2>
            <p class="sub">You will stay signed in on this device.</p>

            <?php if(isset($_GET["changeOk"])) { ?>
                <p class="notice notice-good"><?php showGet("changeOk"); ?></p>
            <?php } ?>

            <?php if(isset($_GET["changeError"])) { ?>
                <p class="notice notice-bad"><?php showGet("changeError"); ?></p>
            <?php } ?>

            <form action="../controllers/passwordControls.php" method="post" onsubmit="return validateChangePassword()">

                <div class="field">
                    <label for="currentPass">Current password</label>
                    <input type="password" id="currentPass" name="currentPass" autocomplete="current-password">
                    <span class="err" id="currentPassErr"><?php showGet("currentPassError"); ?></span>
                </div>

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

                <button type="submit" class="btn">Update password</button>
            </form>

            <p class="switch">
                <a href="<?php echo e($backLink); ?>">Back to dashboard</a>
            </p>
        </div>
    </section>
</body>

</html>
