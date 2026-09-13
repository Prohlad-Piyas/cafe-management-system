<?php
require_once __DIR__ . "/dbConnect.php";







function findUserByEmail($email)
{
    $conn = dbConnection();

    $sql  = "SELECT userId, name, phone, email, pass, role FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $user;
}



function findUserById($userId)
{
    $conn = dbConnection();

    $sql  = "SELECT userId, name, phone, email, pass, role FROM users WHERE userId = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $userId);
    mysqli_stmt_execute($stmt);

    $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $user;
}


function emailExists($email)
{
    return findUserByEmail($email) !== null;
}





function login($email, $pass)
{
    $user = findUserByEmail($email);

    if ($user && password_verify($pass, $user["pass"])) {
        unset($user["pass"]);
        return $user;
    }

    return null;
}





function registerCustomer($name, $phone, $email, $plainPass)
{
    $conn = dbConnection();

    $userId = generateUserId($conn, "customer");
    $hash   = password_hash($plainPass, PASSWORD_DEFAULT);

    $sql  = "INSERT INTO users (userId, name, phone, email, pass, role)
             VALUES (?, ?, ?, ?, ?, 'customer')";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $userId, $name, $phone, $email, $hash);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok ? $userId : null;
}



function generateUserId($conn, $role)
{
    $prefix = [
        "manager"  => "MGR",
        "barista"  => "BAR",
        "waiter"   => "WTR",
        "customer" => "CUS",
    ][$role];

    $sql  = "SELECT COUNT(*) AS total FROM users WHERE role = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $role);
    mysqli_stmt_execute($stmt);

    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    return $prefix . "-" . str_pad($row["total"] + 1, 3, "0", STR_PAD_LEFT);
}





function checkCurrentPassword($userId, $plainPass)
{
    $user = findUserById($userId);
    return $user && password_verify($plainPass, $user["pass"]);
}



function updatePassword($userId, $newPlainPass)
{
    $conn = dbConnection();

    $hash = password_hash($newPlainPass, PASSWORD_DEFAULT);

    $sql  = "UPDATE users SET pass = ? WHERE userId = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $hash, $userId);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}





function createResetToken($userId)
{
    $conn = dbConnection();
    $token     = bin2hex(random_bytes(32));
    $tokenHash = hash("sha256", $token);
    $kill = mysqli_prepare($conn, "UPDATE password_reset SET used = 1 WHERE userId = ?");
    mysqli_stmt_bind_param($kill, "s", $userId);
    mysqli_stmt_execute($kill);
    mysqli_stmt_close($kill);

    
    $sql  = "INSERT INTO password_reset (userId, token_hash, expires_at)
             VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $userId, $tokenHash);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok ? $token : null;
}



function findUserIdByToken($token)
{
    $conn = dbConnection();

    $tokenHash = hash("sha256", $token);

    $sql  = "SELECT userId FROM password_reset
             WHERE token_hash = ? AND used = 0 AND expires_at > NOW()";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $tokenHash);
    mysqli_stmt_execute($stmt);

    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $row ? $row["userId"] : null;
}



function consumeResetToken($token)
{
    $conn = dbConnection();

    $tokenHash = hash("sha256", $token);

    $stmt = mysqli_prepare($conn, "UPDATE password_reset SET used = 1 WHERE token_hash = ?");
    mysqli_stmt_bind_param($stmt, "s", $tokenHash);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}





function updateProfile($userId, $name, $phone)
{
    $conn = dbConnection();

    $sql  = "UPDATE users SET name = ?, phone = ? WHERE userId = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $name, $phone, $userId);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}



function deleteAccount($userId)
{
    $conn = dbConnection();

    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE userId = ?");
    mysqli_stmt_bind_param($stmt, "s", $userId);
    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok;
}





function getAllStaff()
{
    $conn = dbConnection();

    $sql = "SELECT userId, name, phone, email, role, created_at
            FROM users WHERE role IN ('barista','waiter')
            ORDER BY role, name";
    $result = mysqli_query($conn, $sql);
    $staff  = mysqli_fetch_all($result, MYSQLI_ASSOC);

    mysqli_close($conn);

    return $staff;
}



function registerStaff($name, $phone, $email, $plainPass, $role)
{
    if ($role != "barista" && $role != "waiter") {
        return null;
    }

    $conn = dbConnection();

    $userId = generateUserId($conn, $role);
    $hash   = password_hash($plainPass, PASSWORD_DEFAULT);

    $sql  = "INSERT INTO users (userId, name, phone, email, pass, role)
             VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $userId, $name, $phone, $email, $hash, $role);

    $ok = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok ? $userId : null;
}



function deleteStaff($userId)
{
    $conn = dbConnection();

    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE userId = ? AND role IN ('barista','waiter')");
    mysqli_stmt_bind_param($stmt, "s", $userId);
    $ok = mysqli_stmt_execute($stmt);
    $rows = mysqli_stmt_affected_rows($stmt);

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    return $ok && $rows > 0;
}
