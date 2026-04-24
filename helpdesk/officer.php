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
      overflow-x: hidden; /* Prevent horizontal scroll */
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
      background: #4abdb1; /* Darker teal shade */
      min-height: 100vh;
      position: fixed;
      top: 45px; /* height of headbar */
      left: 0;
      color: white;
      z-index: 1000;
    }
    .sidebar a, .sidebar form button {
      display: block;
      color: white;
      padding: 15px;
      text-decoration: none;
      background: none;
      border: none;
      text-align: left;
      width: 100%;
      font-weight: bold;
    }
    .sidebar a:hover, .sidebar form button:hover {
      background: #213cb0;
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
    table, th, td {
      border: 1px solid #ccc;
    }
    th, td {
      padding: 8px;
      text-align: left;
    }
    .btn {
      background: #21736b;
      color: white;
      border: none;
      padding: 6px 10px;
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
$officer = $_SESSION['officer'];
$ename = "";
$ename_query = mysqli_query($conn, "SELECT ename FROM user WHERE user_name = '$officer' LIMIT 1");
if ($ename_row = mysqli_fetch_assoc($ename_query)) {
  $ename = $ename_row['ename'];
}
?>

<div class="navbar">
  <div><strong>Welcome, <?php echo htmlspecialchars($ename); ?></strong></div>
  <div class="text-right"><?php echo date("d M, l"); ?></div>
</div>

<div class="sidebar">
  <form method="post">
    <button name="report">📋 Reports</button>
  </form>
  <a href="ocomplaints.php">➕ Add Complaints</a>
  <a href="logout.php" onclick="return confirm('Do you really want to exit?');">🚪 Logout</a>
</div>

<div class="content">
  <div class="container">
    <div class="col-lg-12">
     <h2 class="text-left">Officer Complaint Register Page</h2>
      <br><br><br>




      <?php
      if (isset($_POST['report']) || isset($_POST['filter_status'])) {
        $deptResult = mysqli_query($conn, "SELECT department FROM user WHERE user_name = '$officer'");
        $deptRow = mysqli_fetch_assoc($deptResult);
        $officerDept = $deptRow['department'];

        $statusFilter = isset($_POST['status']) ? $_POST['status'] : "";

        echo "<h3>Reports</h3>";

        echo "<form method='post' class='mb-3'>
                <input type='hidden' name='filter_status' value='1'>
                <label for='status'><strong>Filter by Status:</strong></label>
                <select name='status' onchange='this.form.submit()'>
                  <option value=''>All</option>
                  <option value='pending' " . ($statusFilter == 'pending' ? 'selected' : '') . ">Pending</option>
                  <option value='In Progress' " . ($statusFilter == 'In Progress' ? 'selected' : '') . ">In Progress</option>
                  <option value='Returned to User' " . ($statusFilter == 'Returned to User' ? 'selected' : '') . ">Returned to User</option>
                  <option value='Rejected' " . ($statusFilter == 'Rejected' ? 'selected' : '') . ">Rejected</option>
                  <option value='Closed' " . ($statusFilter == 'Closed' ? 'selected' : '') . ">Closed</option>
                </select>
                <button type='submit' class='btn'>Apply</button>
              </form>";

        $sel = "SELECT * FROM complaint 
                WHERE department = '$officerDept' 
                AND user_name IN (
                    SELECT user_name FROM user 
                    WHERE user_type = 'user' AND department = '$officerDept'
                )";

        if (!empty($statusFilter)) {
          $sel .= " AND stat = '$statusFilter'";
        }

        $compsel = mysqli_query($conn, $sel);

        if ($compsel && mysqli_num_rows($compsel) > 0) {
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
                    <td><a href='odetails.php?compid={$row['compid']}'>View Complaint</a></td>
                  </tr>";
          }
          echo "</table>";
        } else {
          echo "<p>No complaints found for the selected status.</p>";
        }

        echo "<br><a href='officer.php' class='btn'>Back</a>";
      }
      ?>
    </div>
  </div>
</div>

</body>
</html>