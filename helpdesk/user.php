<?php
include("connection.php");
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Complaint Management</title>
  <link rel="shortcut icon" href="images/ico.ico">   
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/custom.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <script src="js/jquery-3.2.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="css/adminstyles.css">
  <link rel="stylesheet" href="css/mystyle.css">
  <style>
    * {
      box-sizing: border-box;
    }
    html, body {
      margin: 0;
      padding: 0;
      font-family: Arial;
      background: lavender;
      overflow-x: hidden; /* Prevent horizontal scroll */
    }
    .navbar {
      background:rgb(14, 71, 66);
      color: white;
      padding: 10px 20px; /* Reduced height */
      font-size: 20px;
      height: 45px;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 1001;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    .sidebar {
      width: 200px;
      background: #4abdb1;
      height: 100vh;
      position: fixed;
      top: 45px; /* Headbar height */
      left: 0;
      color: white;
      overflow: hidden;
      z-index: 1000;
    }
    .sidebar a {
      display: block;
      color: white;
      padding: 15px;
      text-decoration: none;
    }
    .sidebar a:hover {
      background: #213cb0;
    }
    .content {
      margin-left: 200px;
      margin-top: 65px;
      padding: 20px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table, th, td {
      border: 1px solid #ccc;
    }
    th, td {
      padding: 8px;
      text-align: left;
    }
    .btn {
      padding: 6px 10px;
      background: #21736b;
      color: white;
      border: none;
      cursor: pointer;
    }
    .btn:hover {
      background: #b3ffec;
      color: black;
    }
    .text-right {
      text-align: right;
    }
  </style>
</head>
<body>

<?php
$showProfile = isset($_GET['profile']);
$user = $_SESSION['user'];
$ename = "";
$ename_query = mysqli_query($conn, "SELECT ename FROM user WHERE user_name = '$user' LIMIT 1");
if ($ename_row = mysqli_fetch_assoc($ename_query)) {
  $ename = $ename_row['ename'];
}
?>

<div class="navbar">
  <div><strong>Welcome, <?php echo htmlspecialchars($ename); ?></strong></div>
  <div class="text-right"><?php echo date("d M, l"); ?></div>
</div>

<div class='sidebar'>
  <a href="ucomplaints.php">➕ Add Complaints</a>
  <a href="user.php?profile=true">👤 Profile</a>
  <a href="chpwd.php">🔑 Change Password</a>
  <a href="logout.php" onClick="return confirm('Do you really want to exit ?');">🚪 Logout</a>
</div>

<div class="content">
  <div class="container">
    <div class="col-lg-12">
      <h2 class="text-left">User Complaint Register Page</h2>
      <br><br><br>

      
      <?php
      if ($showProfile) {
        echo "<h3 class='mt-4'>Your Profile Information</h3>";
        $sel = "SELECT * FROM user WHERE user_name = '$user'";
        $compsel = mysqli_query($conn, $sel);

        if (mysqli_num_rows($compsel) > 0) {
          echo "<table><tr>
            <th>Name</th>
            <th>Email</th>
            <th>ID</th>
            <th>Username</th>
            <th>Phone</th>
            <th>Department</th>
            <th>Designation</th>
            <th>User Type</th>
            <th>Registration Date</th>
          </tr>";

          while ($row = mysqli_fetch_assoc($compsel)) {
            echo "<tr>
              <td>{$row['ename']}</td>
              <td>{$row['email']}</td>
              <td>{$row['id']}</td>
              <td>{$row['user_name']}</td>
              <td>{$row['phone']}</td>
              <td>{$row['department']}</td>
              <td>{$row['designation']}</td>
              <td>{$row['user_type']}</td>
              <td>{$row['r_date']}</td>
            </tr>";
          }
          echo "</table>";
        } else {
          echo "<p>No profile data found.</p>";
        }
      }
      ?>

    </div>
  </div>
</div>

<?php include 'jsscript.php'; ?>
</body>
</html>