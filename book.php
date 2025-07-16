<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "hospital";
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
$name = $_POST['name'];
$email = $_POST['email'];
$type = $_POST['type'];
$date = $_POST['date'];
$doctor = $_POST['doctor'];
$sql = "INSERT INTO appointments (name, email, type, date, doctor) VALUES ('$name', '$email', '$type', '$date', '$doctor')";
if ($conn->query($sql) === TRUE) {
  echo "<h2>Appointment booked successfully!</h2>";
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}
$conn->close();
?>