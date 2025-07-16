<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctorName = $_POST['doctor'];
    $_SESSION['doctor'] = $doctorName;
    header("Location: doctor-panel.php");
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Doctor Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Doctor Login</h1>
    <form method="POST">
      <label for="doctor">Enter Your Name:</label>
      <input type="text" name="doctor" required>
      <button type="submit">Login</button>
    </form>
  </div>
</body>
</html>
