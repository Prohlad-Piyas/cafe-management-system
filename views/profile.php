<?php
require_once __DIR__."/../controllers/authGuard.php";

if(!isLoggedIn())
    {
        header("Location: login.php");
        exit;
    }

require_once __DIR__."/../models/userModel.php";

$me=findUserById($_SESSION["userId"]);
$backLink=dashboardFor($_SESSION["role"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Your profile | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/validation.js" defer></script>
</head>

<body class="dash">
    <div class="dash-inner">
        <h1>Your profile</h1>
        <p class="sub">View and update your account details.</p>

        <?php dashNav($_SESSION["role"], "profile", $_SESSION["role"]."/", ""); ?>

        <?php if(isset($_GET["profileOk"])) { ?>
            <p class="notice notice-good"><?php showGet("profileOk"); ?></p>
        <?php } ?>

        <?php if(isset($_GET["profileError"])) { ?>
            <p class="notice notice-bad"><?php showGet("profileError"); ?></p>
        <?php } ?>

        <?php if(isset($_GET["deleteError"])) { ?>
            <p class="notice notice-bad"><?php showGet("deleteError"); ?></p>
        <?php } ?>

        <div class="panel">
            <h2>Account details</h2>

            <form action="../controllers/profileControls.php" method="post">
                <input type="hidden" name="action" value="update">

                <div class="field">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" value="<?php echo e($me["name"]); ?>">
                    <span class="err"><?php showGet("nameError"); ?></span>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo e($me["phone"]); ?>">
                        <span class="err"><?php showGet("phoneError"); ?></span>
                    </div>

                    <div class="field">
                        <label>Email (fixed)</label>
                        <input type="email" value="<?php echo e($me["email"]); ?>" disabled>
                    </div>
                </div>

                <div class="field">
                    <label>User ID / Role</label>
                    <input type="text" value="<?php echo e($me["userId"])." &middot; ".e($me["role"]); ?>" disabled>
                </div>

                <button type="submit" class="btn" style="width:auto;">Save changes</button>
            </form>
        </div>

        <div class="panel">
            <h2>Delete account</h2>
            <p class="sub" style="margin-bottom:16px;">
                This removes your login permanently.
                <?php if($_SESSION["role"]=="customer") { ?>
                    Your order history is deleted with it.
                <?php } ?>
                This cannot be undone.
            </p>

            <form action="../controllers/profileControls.php" method="post"
                  onsubmit="return confirm('Delete your account? This cannot be undone.');">
                <input type="hidden" name="action" value="delete">

                <div class="field">
                    <label for="currentPass">Current password, to confirm</label>
                    <input type="password" id="currentPass" name="currentPass" autocomplete="current-password">
                </div>

                <button type="submit" class="btn btn-danger" style="width:auto;">Delete my account</button>
            </form>
        </div>

        <p class="switch">
            <a href="<?php echo e($backLink); ?>">Back to dashboard</a>
        </p>
    </div>
</body>

</html>
