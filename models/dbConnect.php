<?php
$serverName="localhost";
$userName="root";
$password="";
$db="cafe_db";

function dbConnection()
{
    global $serverName;
    global $userName;
    global $password;
    global $db;
    $conn=mysqli_connect($serverName, $userName, $password, $db);

    if($conn)
    {
        mysqli_set_charset($conn, "utf8mb4");
        return $conn;
    }
    else
    {
        die("connection failed".mysqli_connect_error());
    }
}

?>
