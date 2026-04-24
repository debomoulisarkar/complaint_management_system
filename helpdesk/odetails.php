<?php
include("connection.php");
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial scale=1.0">
    <title>Complaint Details</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <style>
        .embedded-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #ccc;
        }
        iframe {
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<div class="container mt-5">
<?php
if (isset($_GET['compid'])) {
    $compid = intval($_GET['compid']); 

    $stmt = $conn->prepare("SELECT compid, user_name, comp_type, complaint, cdate, stat, fwd, img_name FROM complaint WHERE compid = ?");
    $stmt->bind_param("i", $compid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['update'])) {
                $updatedComplaint = mysqli_real_escape_string($conn, $_POST['complaint']);
                $updateStmt = $conn->prepare("UPDATE complaint SET complaint = ? WHERE compid = ?");
                $updateStmt->bind_param("si", $updatedComplaint, $compid);
                if ($updateStmt->execute()) {
                    echo "<div class='alert alert-success'>Complaint updated successfully.</div>";
                }
                $updateStmt->close();
            }

            if (isset($_POST['forward'])) {
                $remark = mysqli_real_escape_string($conn, $_POST['remark']);
                $currentOfficer = $_SESSION['officer'];
                $date = date("Y-m-d");
                $action = "Forwarded";

                $adminQuery = mysqli_query($conn, "SELECT user_name FROM user WHERE user_type = 'Senior Admin' AND admin_type = 'A0' LIMIT 1");
                $adminRow = mysqli_fetch_assoc($adminQuery);
                $fwdName = $adminRow ? $adminRow['user_name'] : null;

                if (!$fwdName) {
                    echo "<div class='alert alert-danger'>No Senior Admin found. Cannot forward.</div>";
                } else {
                    $movQuery = $conn->prepare("SELECT MAX(mov_no) as max_no FROM movement WHERE compid = ?");
                    $movQuery->bind_param("i", $compid);
                    $movQuery->execute();
                    $movResult = $movQuery->get_result();
                    $rowMov = $movResult->fetch_assoc();
                    $mov_no = $rowMov['max_no'] ? $rowMov['max_no'] + 1 : 1;
                    $movQuery->close();

                    $forwardStmt = $conn->prepare("UPDATE complaint SET fwd = ?, stat = 'In Progress' WHERE compid = ?");
                    $forwardStmt->bind_param("si", $fwdName, $compid);
                    if ($forwardStmt->execute()) {
                        $movementStmt = $conn->prepare("INSERT INTO movement (compid, mov_no, fwd, remarks, rem_date, action, current) VALUES (?, ?, ?, ?, ?, ?, ?)");
                        $movementStmt->bind_param("iisssss", $compid, $mov_no, $fwdName, $remark, $date, $action, $currentOfficer);
                        if ($movementStmt->execute()) {
                            echo "<div class='alert alert-info'>Complaint forwarded to <strong>$fwdName</strong> with remark.</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Failed to record movement.</div>";
                        }
                        $movementStmt->close();
                    }
                    $forwardStmt->close();
                }
            }

            if (isset($_POST['return_user'])) {
                $username = $row['user_name'];
                $currentOfficer = $_SESSION['officer'];
                $remark = mysqli_real_escape_string($conn, $_POST['remark']);
                $date = date("Y-m-d");

                $returnStmt = $conn->prepare("UPDATE complaint SET stat = 'Returned to User', fwd = ? WHERE compid = ?");
                $returnStmt->bind_param("si", $username, $compid);
                if ($returnStmt->execute()) {
                    $movQuery = $conn->prepare("SELECT MAX(mov_no) as max_no FROM movement WHERE compid = ?");
                    $movQuery->bind_param("i", $compid);
                    $movQuery->execute();
                    $movResult = $movQuery->get_result();
                    $rowMov = $movResult->fetch_assoc();
                    $mov_no = $rowMov['max_no'] ? $rowMov['max_no'] + 1 : 1;
                    $movQuery->close();

                    $movementStmt = $conn->prepare("INSERT INTO movement (compid, mov_no, fwd, remarks, rem_date, action, current) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $action = "Returned to User";
                    $movementStmt->bind_param("iisssss", $compid, $mov_no, $username, $remark, $date, $action, $currentOfficer);
                    $movementStmt->execute();
                    $movementStmt->close();

                    echo "<div class='alert alert-warning'>Complaint returned to user: <strong>$username</strong>.</div>";
                }
                $returnStmt->close();
            }
        }
?>

    <h2>Complaint Details</h2>
    <form method="POST">
        <table class="table table-bordered">
            <tr>
                <th>Complaint ID</th>
                <td><?php echo $row['compid']; ?></td>
            </tr>
            <tr>
                <th>Username</th>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
            </tr>
            <tr>
                <th>Type</th>
                <td><?php echo htmlspecialchars($row['comp_type']); ?></td>
            </tr>
            <tr>
                <th>Date Filed</th>
                <td><?php echo $row['cdate']; ?></td>
            </tr>
            <tr>
                <th>Forwarded To</th>
                <td><?php echo $row['fwd'] ? htmlspecialchars($row['fwd']) : 'Not forwarded yet'; ?></td>
            </tr>
            <tr>
                <th>Attached File</th>
                <td>
                    <?php
                    $filename = $row['img_name'];
                    $filePath = "uploads/" . $filename;

                    if (!empty($filename) && file_exists($filePath)) {
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='" . htmlspecialchars($filePath) . "' class='embedded-image' alt='Complaint Image'>";
                        } elseif ($ext === 'pdf') {
                            echo "<iframe src='" . htmlspecialchars($filePath) . "' width='100%' height='600px'></iframe>";
                        } else {
                            echo "Unsupported file type.";
                        }

                        echo "<br><a href='" . htmlspecialchars($filePath) . "' target='_blank' class='btn btn-outline-primary btn-sm mt-2'>Download File</a>";
                    } else {
                        echo "No file uploaded.";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Description (Editable)</th>
                <td>
                    <textarea name="complaint" rows="6" class="form-control"><?php echo htmlspecialchars($row['complaint']); ?></textarea>
                </td>
            </tr>
            <tr>
                <th>Status</th>
                <td><strong><?php echo $row['stat']; ?></strong></td>
            </tr>
            <tr>
                <th>Remark</th>
                <td>
                    <textarea name="remark" rows="4" class="form-control" placeholder="Enter your remark here..."></textarea>
                </td>
            </tr>
        </table>

        <button type="submit" name="update" class="btn btn-primary">Save</button>
        <button type="submit" name="forward" class="btn btn-info">Forward to Senior Admin</button>
        <button type="submit" name="return_user" class="btn btn-warning">Return to User</button>
        <a href="officer.php" class="btn btn-secondary">Back</a>
    </form>

<?php
    } else {
        echo "<div class='alert alert-warning'>No complaint found for the provided ID.</div>";
    }

    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>No complaint ID provided in the request.</div>";
}
?>
</div>

</body>
</html>