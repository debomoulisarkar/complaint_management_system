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

    $stmt = $conn->prepare("SELECT * FROM complaint WHERE compid = ?");
    $stmt->bind_param("i", $compid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['send_back'])) {
                $remark = mysqli_real_escape_string($conn, $_POST['remark']);
                $username = $row['user_name'];
                $currentAdmin = $_SESSION['admin'];

                $movQuery = $conn->prepare("SELECT MAX(mov_no) AS maxno FROM movement WHERE compid = ?");
                $movQuery->bind_param("i", $compid);
                $movQuery->execute();
                $movResult = $movQuery->get_result();
                $movRow = $movResult->fetch_assoc();
                $mov_no = $movRow['maxno'] ? $movRow['maxno'] + 1 : 1;
                $movQuery->close();

                $insert = $conn->prepare("INSERT INTO movement (remarks, compid, mov_no, fwd, current, rem_date, action) 
                                          VALUES (?, ?, ?, ?, ?, NOW(), 'Returned to User')");
                $insert->bind_param("sisss", $remark, $compid, $mov_no, $username, $currentAdmin);
                $insert->execute();
                $insert->close();

                $updateComp = $conn->prepare("UPDATE complaint SET stat = 'Returned to User', fwd = ? WHERE compid = ?");
                $updateComp->bind_param("si", $username, $compid);
                if ($updateComp->execute()) {
                    echo "<div class='alert alert-info'>Complaint sent back to user with remark.</div>";
                }
                $updateComp->close();
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
                <th>Attached File</th>
                <td>
                    <?php
                    $filename = $row['img_name'];
                    $filepath = "uploads/" . $filename;

                    if (!empty($filename) && file_exists($filepath)) {
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='" . htmlspecialchars($filepath) . "' class='embedded-image' alt='Complaint Image'>";
                        } elseif ($ext === 'pdf') {
                            echo "<iframe src='" . htmlspecialchars($filepath) . "' width='100%' height='600px'></iframe>";
                        } else {
                            echo "Unsupported file type.";
                        }

                        echo "<br><a href='" . htmlspecialchars($filepath) . "' target='_blank' class='btn btn-outline-primary btn-sm mt-2'>Download File</a>";
                    } else {
                        echo "No file uploaded.";
                    }
                    ?>
                </td>
            </tr>
        </table>

        <button type="submit" name="send_back" class="btn btn-warning">Return to User</button>
        <a href="admins.php" class="btn btn-secondary">Back</a>
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