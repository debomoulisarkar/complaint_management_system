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
            if (isset($_POST['return_user'])) {
                $username = $row['user_name'];
                $currentAdmin = $_SESSION['admin'];
                $remark = mysqli_real_escape_string($conn, $_POST['remark']);
                $date = date("Y-m-d");

                // Update complaint table
                $returnStmt = $conn->prepare("UPDATE complaint SET stat = 'Returned to User', fwd = ? WHERE compid = ?");
                $returnStmt->bind_param("si", $username, $compid);
                if ($returnStmt->execute()) {
                    // Insert into movement table
                    $movQuery = $conn->prepare("SELECT MAX(mov_no) as max_no FROM movement WHERE compid = ?");
                    $movQuery->bind_param("i", $compid);
                    $movQuery->execute();
                    $movResult = $movQuery->get_result();
                    $rowMov = $movResult->fetch_assoc();
                    $mov_no = $rowMov['max_no'] ? $rowMov['max_no'] + 1 : 1;
                    $movQuery->close();

                    $movementStmt = $conn->prepare("INSERT INTO movement (compid, mov_no, fwd, remarks, rem_date, action, current) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $action = "Returned to User";
                    $movementStmt->bind_param("iisssss", $compid, $mov_no, $username, $remark, $date, $action, $currentAdmin);
                    $movementStmt->execute();
                    $movementStmt->close();

                    echo "<div class='alert alert-warning'>Complaint returned to user: <strong>$username</strong>.</div>";
                }
                $returnStmt->close();
            }

            if (isset($_POST['forward_admin'])) {
                $forwardTo = $_POST['forward_to'];
                $remark = mysqli_real_escape_string($conn, $_POST['remark']);
                $currentAdmin = $_SESSION['admin'];

                $movQuery = $conn->prepare("SELECT MAX(mov_no) AS maxno FROM movement WHERE compid = ?");
                $movQuery->bind_param("i", $compid);
                $movQuery->execute();
                $movResult = $movQuery->get_result();
                $movRow = $movResult->fetch_assoc();
                $mov_no = $movRow['maxno'] ? $movRow['maxno'] + 1 : 1;
                $movQuery->close();

                $insert = $conn->prepare("INSERT INTO movement (remarks, compid, mov_no, fwd, current, rem_date, action) VALUES (?, ?, ?, ?, ?, NOW(), 'Forwarded to Admin')");
                $insert->bind_param("sisss", $remark, $compid, $mov_no, $forwardTo, $currentAdmin);
                $insert->execute();
                $insert->close();

                $updateComp = $conn->prepare("UPDATE complaint SET stat = 'Forwarded to Admin', fwd = ? WHERE compid = ?");
                $updateComp->bind_param("si", $forwardTo, $compid);
                if ($updateComp->execute()) {
                    echo "<div class='alert alert-info'>Complaint forwarded to <strong>$forwardTo</strong> successfully.</div>";
                }
                $updateComp->close();
            }
        }

        // Admin list
        $admins = [];
        $currentAdmin = $_SESSION['admin'];
        $adminQuery = mysqli_query($conn, "SELECT user_name FROM user WHERE user_type = 'admin' AND user_name != '$currentAdmin'");
        while ($adminRow = mysqli_fetch_assoc($adminQuery)) {
            $admins[] = $adminRow['user_name'];
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
                <th>Description</th>
                <td><?php echo nl2br(htmlspecialchars($row['complaint'])); ?></td>
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
            <tr>
                <th>Forward To Admin</th>
                <td>
                    <select name="forward_to" class="form-control">
                        <option value="">-- Select Admin --</option>
                        <?php foreach ($admins as $adminName): ?>
                            <option value="<?php echo htmlspecialchars($adminName); ?>"><?php echo htmlspecialchars($adminName); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        </table>

        <button type="submit" name="forward_admin" class="btn btn-info">Forward to Admin</button>
        <button type="submit" name="return_user" class="btn btn-warning">Return to User</button>
        <a href="admin0.php" class="btn btn-secondary">Back</a>
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