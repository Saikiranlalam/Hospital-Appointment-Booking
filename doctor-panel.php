<?php
session_start();
if (!isset($_SESSION['doctor'])) {
  header("Location: doctor-login.php");
  exit();
}
$doctorName = $_SESSION['doctor'];
$conn = new mysqli("localhost", "root", "", "hospital");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && isset($_POST['appointment_id'])) {
    $action = $_POST['action'];
    $id = (int) $_POST['appointment_id'];
    $status = ($action == 'approve') ? 'Approved' : 'Rejected';
    $conn->query("UPDATE appointments SET status = '$status' WHERE id = $id");

    $resultMail = $conn->query("SELECT email, name FROM appointments WHERE id = $id");
    if ($rowMail = $resultMail->fetch_assoc()) {
        $to = $rowMail['email'];
        $subject = "Appointment Status Update";
        $message = "Hello " . $rowMail['name'] . ",\n\nYour appointment with Dr. $doctorName has been $status.\n\nThank you.";
        $headers = "From: hospital@example.com";
        mail($to, $subject, $message, $headers);
    }
}

$filterName = $_GET['search'] ?? '';
$filterStatus = $_GET['status'] ?? '';
if ($filterName !== '' || $filterStatus !== '') {
  $sql = "SELECT * FROM appointments WHERE doctor = ?";
  $params = [$doctorName];
  $types = "s";
  if ($filterName !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = "%$filterName%";
    $types .= "s";
  }
  if ($filterStatus !== '') {
    $sql .= " AND status = ?";
    $params[] = $filterStatus;
    $types .= "s";
  }
  $sql .= " ORDER BY date ASC";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types, ...$params);
} else {
  $stmt = $conn->prepare("SELECT * FROM appointments WHERE doctor = ? ORDER BY date ASC");
  $stmt->bind_param("s", $doctorName);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Doctor Panel</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h1>Appointments for Dr. <?= htmlspecialchars($doctorName) ?></h1>
    <form method="GET" style="margin-bottom:20px;">
      <input type="text" name="search" placeholder="Search by patient name" value="<?= htmlspecialchars($filterName) ?>">
      <select name="status">
        <option value="">--Filter by Status--</option>
        <option value="Pending" <?= $filterStatus === 'Pending' ? 'selected' : '' ?>>Pending</option>
        <option value="Approved" <?= $filterStatus === 'Approved' ? 'selected' : '' ?>>Approved</option>
        <option value="Rejected" <?= $filterStatus === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
      <button type="submit">Filter</button>
    </form>
    <table border="1" cellpadding="10" style="width:100%; margin-top:20px;">
      <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Date</th><th>Status</th><th>Action</th>
      </tr>
      <?php while($row = $result->fetch_assoc()) { ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['email'] ?></td>
        <td><?= $row['type'] ?></td>
        <td><?= $row['date'] ?></td>
        <td><?= $row['status'] ?? 'Pending' ?></td>
        <td>
          <?php if (($row['status'] ?? '') !== 'Approved') { ?>
            <form method="POST" style="display:inline;">
              <input type="hidden" name="appointment_id" value="<?= $row['id'] ?>">
              <button type="submit" name="action" value="approve">Approve</button>
              <button type="submit" name="action" value="reject">Reject</button>
            </form>
          <?php } else { echo 'Approved'; } ?>
        </td>
      </tr>
      <?php } ?>
    </table>
    <p style="text-align:center; margin-top:20px;">
      <a href="logout.php">Logout</a>
    </p>
  </div>
</body>
</html>
