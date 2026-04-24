<?php
include("connection.php");
session_start();

if (!isset($_GET['compid'])) {
    echo "No complaint ID provided.";
    exit;
}

$compid = intval($_GET['compid']);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Remarks for Complaint #<?php echo $compid; ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        body { font-family: Arial; background: lavender; padding: 20px; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        h2 { margin-bottom: 20px; }
        .back-link { margin-top: 20px; display: inline-block; }
    </style>
</head>
<body>

<h2>Remarks for Complaint ID #<?php echo $compid; ?></h2>

<?php
$query = $conn->prepare("SELECT mov_no, remarks, current, fwd, rem_date, action FROM movement WHERE compid = ? ORDER BY mov_no ASC");
$query->bind_param("i", $compid);
$query->execute();
$result = $query->get_result();

if ($result->num_rows > 0) {
    echo "<table>
            <tr>
                <th>Movement No</th>
                <th>Remark</th>
                <th>Written By</th>
                <th>Forwarded To</th>
                <th>Date</th>
                <th>Action</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['mov_no']}</td>
                <td>" . htmlspecialchars($row['remarks']) . "</td>
                <td>{$row['current']}</td>
                <td>{$row['fwd']}</td>
                <td>{$row['rem_date']}</td>
                <td>{$row['action']}</td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No remarks found for this complaint.</p>";
}

$query->close();
?>

<a class="btn btn-primary back-link" href="ucomplaints.php?section=status">← Back to Complaints</a>

</body>
</html>
