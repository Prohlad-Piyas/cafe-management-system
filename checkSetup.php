<?php


require_once __DIR__ . "/models/dbConnect.php";

$checks = [];

function addCheck(&$checks, $name, $ok, $detail, $fix = "")
{
    $checks[] = ["name" => $name, "ok" => $ok, "detail" => $detail, "fix" => $fix];
}


addCheck($checks,
    "PHP version",
    version_compare(PHP_VERSION, "7.4", ">="),
    PHP_VERSION,
    "This project needs PHP 7.4 or newer. Update XAMPP."
);


addCheck($checks,
    "mysqli extension",
    extension_loaded("mysqli"),
    extension_loaded("mysqli") ? "loaded" : "missing",
    "In php.ini remove the ; in front of extension=mysqli, then restart Apache."
);


$sessionOk = false;
if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}
$sessionOk = session_status() === PHP_SESSION_ACTIVE;
addCheck($checks,
    "Sessions",
    $sessionOk,
    $sessionOk ? "working" : "could not start",
    "Check that the folder in session.save_path (php.ini) exists and is writable."
);


mysqli_report(MYSQLI_REPORT_OFF);
$serverConn = @mysqli_connect($serverName, $userName, $password, "");
addCheck($checks,
    "MySQL server",
    (bool) $serverConn,
    $serverConn ? "connected as '" . $userName . "'" : mysqli_connect_error(),
    "Start MySQL in the XAMPP control panel. If it will not start, port 3306 "
    . "is taken by another program -- see SETUP_XAMPP.md."
);


$dbExists = false;
if ($serverConn) {
    $res = mysqli_query($serverConn, "SHOW DATABASES LIKE '" . $db . "'");
    $dbExists = $res && mysqli_num_rows($res) > 0;
}
addCheck($checks,
    "Database '" . $db . "'",
    $dbExists,
    $dbExists ? "found" : "not found",
    "Run database/dbCreation.php in the browser."
);


$tablesOk = false;
$foundTables = [];
$needed = ["address", "users", "password_reset"];
if ($dbExists) {
    $conn = @mysqli_connect($serverName, $userName, $password, $db);
    if ($conn) {
        $res = mysqli_query($conn, "SHOW TABLES");
        while ($row = mysqli_fetch_array($res)) {
            $foundTables[] = $row[0];
        }
        $tablesOk = count(array_diff($needed, $foundTables)) === 0;
    }
}
addCheck($checks,
    "Tables",
    $tablesOk,
    $foundTables ? implode(", ", $foundTables) : "none",
    "Run database/tableCreation.php in the browser."
);


$userCount = 0;
if ($tablesOk) {
    $res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM users");
    $userCount = (int) mysqli_fetch_assoc($res)["c"];
}
addCheck($checks,
    "Accounts in users table",
    $userCount > 0,
    $userCount . " row(s)",
    "Run database/insertOp.php once in the browser, then delete it."
);


$base = rtrim(dirname($_SERVER["SCRIPT_NAME"]), "/");
$folderOk = strtolower(basename(__DIR__)) === "cafe";
addCheck($checks,
    "Folder name",
    $folderOk,
    basename(__DIR__),
    "Rename the folder to exactly 'cafe' inside htdocs, so links resolve."
);

$allOk = true;
foreach ($checks as $c) {
    if (!$c["ok"]) {
        $allOk = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Setup check | Chalk &amp; Bean</title>
    <link rel="stylesheet" href="views/css/style.css">
</head>

<body class="dash">
    <div class="dash-inner">
        <h1>Setup check</h1>
        <p class="sub">
            Everything this project needs on XAMPP. Fix the red rows top to bottom.
        </p>

        <?php if ($allOk) { ?>
            <p class="notice notice-good">
                All checks passed. Open
                <a href="<?php echo htmlspecialchars($base, ENT_QUOTES, "UTF-8"); ?>/index.php">the app</a>,
                then delete <code>checkSetup.php</code> and <code>database/insertOp.php</code>.
            </p>
        <?php } else { ?>
            <p class="notice notice-bad">
                Some checks failed. Each one below says how to fix it.
            </p>
        <?php } ?>

        <table class="check-table">
            <thead>
                <tr>
                    <th>Check</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checks as $c) { ?>
                    <tr>
                        <td>
                            <span class="<?php echo $c["ok"] ? "tick" : "cross"; ?>">
                                <?php echo $c["ok"] ? "PASS" : "FAIL"; ?>
                            </span>
                            <?php echo htmlspecialchars($c["name"], ENT_QUOTES, "UTF-8"); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($c["detail"], ENT_QUOTES, "UTF-8"); ?>
                            <?php if (!$c["ok"] && $c["fix"] !== "") { ?>
                                <p class="fix"><?php echo htmlspecialchars($c["fix"], ENT_QUOTES, "UTF-8"); ?></p>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <p class="sub">
            This app is being served from
            <code>http://localhost<?php echo htmlspecialchars($base, ENT_QUOTES, "UTF-8"); ?>/</code>
        </p>
    </div>
</body>

</html>
