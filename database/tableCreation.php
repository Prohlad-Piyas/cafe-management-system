<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe_db";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}



$sql = "CREATE TABLE address (
aid INT AUTO_INCREMENT PRIMARY KEY,
city VARCHAR(30) NOT NULL,
country VARCHAR(30) NOT NULL DEFAULT 'Bangladesh'
)";

if (mysqli_query($conn, $sql)) {
  echo "address table created successfully<br>";
} else {
  echo "Error creating address table: " . mysqli_error($conn) . "<br>";
}



$sql = "CREATE TABLE users (
userId VARCHAR(20) PRIMARY KEY,
name VARCHAR(50) NOT NULL,
phone VARCHAR(15) NOT NULL,
email VARCHAR(50) NOT NULL UNIQUE,
pass VARCHAR(255) NOT NULL,
role ENUM('manager','barista','waiter','customer') NOT NULL DEFAULT 'customer',
aid INT NULL,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT users_aid_fk FOREIGN KEY (aid) REFERENCES address (aid) ON DELETE SET NULL
)";

if (mysqli_query($conn, $sql)) {
  echo "users table created successfully<br>";
} else {
  echo "Error creating users table: " . mysqli_error($conn) . "<br>";
}



$sql = "CREATE TABLE password_reset (
prid INT AUTO_INCREMENT PRIMARY KEY,
userId VARCHAR(20) NOT NULL,
token_hash CHAR(64) NOT NULL,
expires_at DATETIME NOT NULL,
used TINYINT(1) NOT NULL DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
CONSTRAINT reset_user_fk FOREIGN KEY (userId) REFERENCES users (userId) ON DELETE CASCADE
)";

if (mysqli_query($conn, $sql)) {
  echo "password_reset table created successfully<br>";
} else {
  echo "Error creating password_reset table: " . mysqli_error($conn) . "<br>";
}

echo "<br>Now run insertOp.php";

mysqli_close($conn);
?>
