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
  <title>Complaint Management - Admin</title>
  <link rel="shortcut icon" href="images/ico.ico">   
  <link rel="stylesheet" href="css/bootstrap.css">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/custom.css">
  <link rel="stylesheet" href="css/adminstyles.css">
  <link rel="stylesheet" href="css/mystyle.css">
  <script src="js/jquery-3.2.1.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <style>
    * {
      box-sizing: border-box;
    }
    html, body {
      margin: 0;
      padding: 0;
      font-family: Arial;
      background: lavender;
      overflow-x: hidden;
    }
    .navbar {
      background:rgb(14, 71, 66);
      color: white;
      padding: 10px 20px;
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
      min-height: 100vh;
      position: fixed;
      top: 45px;
      left: 0;
      color: white;
      z-index: 1000;
    }
    .sidebar a, .sidebar button {
      display: block;
      color: white;
      padding: 15px;
      text-decoration: none;
      background: none;
      border: none;
      text-align: left;
      width: 100%;
      font-size: 16px;
      font-weight: bold;
    }
    .sidebar a:hover, .sidebar button:hover {
      background: #213cb0;
      color: white;
      cursor: pointer;
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
    th, td {
      border: 1px solid #ccc;
      padding: 8px;
      text-align: left;
    }
    .btn-action {
      padding: 6px 10px;
      background: #21736b;
      color: white;
      border: none;
      cursor: pointer;
    }
    .btn-action:hover {
      background: #b3ffec;
      color: black;
    }
  </style>
</head>
<body>

<?php
$admin = $_SESSION['admin'];
$ename = "";
$ename_query = mysqli_query($conn, "SELECT ename FROM user WHERE user_name = '$admin' LIMIT 1");
if ($ename_row = mysqli_fetch_assoc($ename_query)) {
  $ename = $ename_row['ename'];
}
?>

<div class="navbar">
  <div><strong>Welcome, <?php echo htmlspecialchars($ename); ?></strong></div>
  <div class="text-right"><?php echo date("d M, l"); ?></div>
</div>

<div class="sidebar">
  <form action="" method="post">
    <button name="report">📋 View Assigned Reports</button>
  </form>
  <a href="logout.php" onclick="return confirm('Do you really want to exit ?');">🚪 Logout</a>
</div>

<div class="content">
  <div class="container">
    <h2>Admin Response Page</h2>
    <br>

    

    <div class="col-lg-12 mt-4">
      <p>Your complaint will be recorded and handled by the appropriate team/person in your department with IT’s assistance.</p>
    </div>

    <div class="complains mt-4">
      <form action="" method="post">
        <?php
        if (isset($_POST['report'])) {
          $loggedInAdmin = $_SESSION['admin'];

          $stmt = $conn->prepare("SELECT * FROM complaint WHERE fwd = ? ORDER BY compid DESC");
          $stmt->bind_param("s", $loggedInAdmin);
          $stmt->execute();
          $compsel = $stmt->get_result();

          if ($compsel->num_rows > 0) {
            echo "<h3 class='mt-4'>Complaints Forwarded to You</h3>";
            echo "<table>
                    <tr>
                      <th>ID</th>
                      <th>Username</th>
                      <th>Type</th>
                      <th>Department</th>
                      <th>Complaint</th>
                      <th>Forwarded To</th>
                      <th>Date</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>";
            while ($row = $compsel->fetch_assoc()) {
              echo "<tr>
                      <td>{$row['compid']}</td>
                      <td>{$row['user_name']}</td>
                      <td>{$row['comp_type']}</td>
                      <td>{$row['department']}</td>
                      <td>{$row['complaint']}</td>
                      <td>{$row['fwd']}</td>
                      <td>{$row['cdate']}</td>
                      <td>{$row['stat']}</td>
                      <td><a href='adetails.php?compid={$row['compid']}'>View</a></td>
                    </tr>";
            }
            echo "</table>";
          } else {
            echo "<p>No complaints have been forwarded to you.</p>";
          }

          $stmt->close();
        }
        ?>
      </form>
    </div>
  </div>
</div>

</body>
</html>