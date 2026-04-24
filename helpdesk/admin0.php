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
    .form-control {
      display: inline-block;
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
    <button name="report">📋 View All Reports</button>
  </form>
  <a href="logout.php" onclick="return confirm('Do you really want to exit ?');">🚪 Logout</a>
</div>

<div class="content">
  <div class="container">
    <h2>Senior Admin Response Page</h2>
    <br>
    

    <div class="complains mt-4">
      <form action="" method="post">
        <?php
        $status_filter = isset($_POST['status_filter']) ? $_POST['status_filter'] : '';
        $type_filter = isset($_POST['type_filter']) ? $_POST['type_filter'] : '';
        ?>
        <div class="form-row mb-3">
          <label>Status:</label>
          <select name="status_filter" class="form-control" style="width:200px; margin-right:10px;">
            <option value="">All</option>
            <option value="pending" <?php if ($status_filter == 'pending') echo 'selected'; ?>>Pending</option>
            <option value="In Progress" <?php if ($status_filter == 'In Progress') echo 'selected'; ?>>In Progress</option>
            <option value="Rejected" <?php if ($status_filter == 'Rejected') echo 'selected'; ?>>Rejected</option>
            <option value="Returned to User" <?php if ($status_filter == 'Returned to User') echo 'selected'; ?>>Returned to User</option>
            <option value="Closed" <?php if ($status_filter == 'Closed') echo 'selected'; ?>>Closed</option>
          </select>
          <label>Type:</label>
          <select name="type_filter" class="form-control" style="width:200px; margin-right:10px;">
            <option value="">All</option>
            <option value="Hardware" <?php if ($type_filter == 'Hardware') echo 'selected'; ?>>Hardware</option>
            <option value="Software" <?php if ($type_filter == 'Software') echo 'selected'; ?>>Software</option>
            <option value="Others" <?php if ($type_filter == 'Others') echo 'selected'; ?>>Others</option>
          </select>
          <button name="report" class="btn btn-primary">Apply Filters</button>
        </div>

        <?php
        if (isset($_POST['report'])) {
          $sel = "SELECT * FROM complaint WHERE 1=1";
          if ($status_filter != '') {
            $sel .= " AND stat = '" . mysqli_real_escape_string($conn, $status_filter) . "'";
          }
          if ($type_filter != '') {
            $sel .= " AND comp_type = '" . mysqli_real_escape_string($conn, $type_filter) . "'";
          }
          $sel .= " ORDER BY compid DESC";

          $compsel = mysqli_query($conn, $sel);

          if ($compsel && mysqli_num_rows($compsel) > 0) {
            echo "<h3 class='mt-4'>Filtered Complaints</h3>";
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
            while ($row = mysqli_fetch_assoc($compsel)) {
              echo "<tr>
                      <td>{$row['compid']}</td>
                      <td>{$row['user_name']}</td>
                      <td>{$row['comp_type']}</td>
                      <td>{$row['department']}</td>
                      <td>{$row['complaint']}</td>
                      <td>{$row['fwd']}</td>
                      <td>{$row['cdate']}</td>
                      <td>{$row['stat']}</td>
                      <td><a href='a0details.php?compid={$row['compid']}'>View</a></td>
                    </tr>";
            }
            echo "</table>";
          } else {
            echo "<p>No complaints found.</p>";
          }
        }
        ?>
      </form>
    </div>
  </div>
</div>

</body>
</html>