<?php
require_once __DIR__ . "/../../controllers/authGuard.php";


requireRole("waiter");
?>
<!DOCTYPE html>
<html>

<head>
   
    <title>Waiter floor | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="dash">
    <div class="dash-inner">
        <h1>Waiter floor</h1>
        <p class="sub">Page 4 will show which tables are waiting.</p>

        <dl>
            <dt>Signed in as</dt>
            <dd><?php echo e($_SESSION["name"]); ?></dd>

            <dt>User ID</dt>
            <dd><?php echo e($_SESSION["userId"]); ?></dd>

            <dt>Role</dt>
            <dd><?php echo e($_SESSION["role"]); ?></dd>
        </dl>

        <p class="dash-actions">
            <a class="btn btn-plain" href="../changePassword.php">Change password</a>
            <a class="btn btn-plain" href="../logout.php">Sign out</a>
        </p>
    </div>
</body>

</html>
