<?php
require_once __DIR__ . "/../../controllers/authGuard.php";
requireRole("manager");

require_once __DIR__ . "/../../models/userModel.php";

$staff=getAllStaff();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff accounts | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body class="dash dash-wide">
    <div class="dash-inner">
        <h1>Staff accounts</h1>
        <p class="sub">Create and remove barista and waiter logins.</p>

        <?php dashNav("manager", "staff"); ?>

        <?php if(isset($_GET["staffOk"])) { ?>
            <p class="notice notice-good"><?php showGet("staffOk"); ?></p>
        <?php } ?>

        <?php if(isset($_GET["staffError"])) { ?>
            <p class="notice notice-bad"><?php showGet("staffError"); ?></p>
        <?php } ?>

        <div class="panel" style="max-width:560px;">
            <h2>Add a staff account</h2>

            <form action="../../controllers/staffControls.php" method="post">
                <input type="hidden" name="action" value="add">

                <div class="field">
                    <label for="name">Full name</label>
                    <input type="text" id="name" name="name" value="<?php showGet("name"); ?>">
                    <span class="err"><?php showGet("nameError"); ?></span>
                </div>

                <div class="form-row">
                    <div class="field">
                        <label for="phone">Phone</label>
                        <input type="tel" id="phone" name="phone">
                        <span class="err"><?php showGet("phoneError"); ?></span>
                    </div>

                    <div class="field">
                        <label for="role">Role</label>
                        <select id="role" name="role">
                            <option value="barista">Barista</option>
                            <option value="waiter">Waiter</option>
                        </select>
                        <span class="err"><?php showGet("roleError"); ?></span>
                    </div>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email">
                    <span class="err"><?php showGet("emailError"); ?></span>
                </div>

                <div class="field">
                    <label for="pass">Temporary password</label>
                    <input type="text" id="pass" name="pass" placeholder="At least 8 characters">
                    <span class="err"><?php showGet("passError"); ?></span>
                </div>

                <button type="submit" class="btn" style="width:auto;">Create account</button>
            </form>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Staff ID</th>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($staff)==0) { ?>
                    <tr class="muted-row"><td colspan="6">No barista or waiter accounts yet.</td></tr>
                <?php } ?>

                <?php foreach($staff as $person) { ?>
                    <tr>
                        <td><?php echo e($person["userId"]); ?></td>
                        <td><?php echo e($person["name"]); ?></td>
                        <td><?php echo e(ucfirst($person["role"])); ?></td>
                        <td><?php echo e($person["phone"]); ?></td>
                        <td><?php echo e($person["email"]); ?></td>
                        <td>
                            <form action="../../controllers/staffControls.php" method="post"
                                  onsubmit="return confirm('Remove this staff account?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="userId" value="<?php echo e($person["userId"]); ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>
