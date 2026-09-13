<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cafe_db";
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}



$sql = "INSERT INTO address (city, country) VALUES
('Dhaka','Bangladesh'),
('Gazipur','Bangladesh'),
('Rangpur','Bangladesh')";

if (mysqli_query($conn, $sql)) {
  echo "address rows inserted successfully<br><br>";
} else {
  echo "Error: " . mysqli_error($conn) . "<br><br>";
}



$sql = "INSERT INTO users (userId, name, phone, email, pass, role)
VALUES (?,?,?,?,?,?)";

$stmt = mysqli_prepare($conn, $sql);

$demoUsers = [
  ["MGR-001", "Atif Rahman",  "01711000001", "manager@cafe.com", "Manager123",  "manager"],
  ["BAR-001", "Rely Black",   "01766096733", "barista@cafe.com", "Barista123",  "barista"],
  ["WTR-001", "Rony Ahmed",   "01897542741", "waiter@cafe.com",  "Waiter123",   "waiter"],
  ["CUS-001", "Jenny Tasnim", "01778555331", "jenny@cafe.com",   "Customer123", "customer"],
];

foreach ($demoUsers as $u) {
  $userId = $u[0];
  $name   = $u[1];
  $phone  = $u[2];
  $email  = $u[3];
  $plain  = $u[4];
  $role   = $u[5];

  $hash = password_hash($plain, PASSWORD_DEFAULT);

  mysqli_stmt_bind_param($stmt, 'ssssss', $userId, $name, $phone, $email, $hash, $role);

  if (mysqli_stmt_execute($stmt)) {
    echo "created " . $role . " &nbsp; " . $email . " &nbsp; / &nbsp; " . $plain . "<br>";
  } else {
    echo "Error inserting " . $email . ": " . mysqli_stmt_error($stmt) . "<br>";
  }
}

echo "<br>Write these down, then DELETE this file.<br>";
echo "Now open <a href='../index.php'>the app</a>";

mysqli_close($conn);
?>
