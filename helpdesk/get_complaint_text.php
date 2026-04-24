<?php
include("connection.php");
session_start();

if (!isset($_GET['compid']) || !is_numeric($_GET['compid'])) {
    http_response_code(400);
    echo "Invalid complaint ID.";
    exit;
}

$compid = (int)$_GET['compid'];
$user = $_SESSION['user'];

$query = "SELECT complaint FROM complaint WHERE compid = $compid AND user_name = '$user' AND stat = 'Returned to User' LIMIT 1";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) === 1) {
    $row = mysqli_fetch_assoc($result);
    echo htmlspecialchars($row['complaint']);
} else {
    http_response_code(404);
    echo "Complaint not found or not eligible for resubmission.";
}
?>