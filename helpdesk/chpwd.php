<?php
include("connection.php");
session_start();
$msg = '';
if (isset($_POST['backbtn'])) { header("Location: user.php"); }
if (isset($_POST['submit'])) {

    $l_username = $_POST['username'];
    $l_password = $_POST['password'];
	$l_cnfpassword = $_POST['cnfpassword'];
	$l_newpassword = $_POST['newpassword'];
	
	if ($l_password==$l_cnfpassword) {

    $select1 = "SELECT * FROM user WHERE user_name = '$l_username' AND pwd = '$l_password'";
    $select_user = mysqli_query($conn, $select1);

    if (mysqli_num_rows($select_user) > 0) {
			$table_upd ="UPDATE user SET pwd='$l_newpassword' WHERE user_name = '$l_username' AND pwd='$l_password'";
            $table=mysqli_query($conn,$table_upd);
			
			$msg = "Password changed Successfully!";
			
            header("Location: user.php");
        } 
	else {
		$msg = "No Record found with this Password ...!";
		}
}
 else {
		$msg = "Password does not match!";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial scale=1.0">
    <title>Change Password</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap.css">
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="css/adminstyles.css">
	<link rel="stylesheet" href="css/mystyle.css">
    <style>
.FieldInfo{
    color: rgb(251, 174, 44);
    font-family: Bitter,Georgia,"Times New Roman",Times,serif;
    font-size: 1.2em;
}
.roundborder {
  border: 2px solid blue;
  border-radius: 12px;
}
body{ 
	background-color: lavender;
}	
.SystemInfo {
	font-family: Bitter,Georgia,"Times New Roman",Times,serif;
	font-size: 3em;
}	
.center {
  margin: 0;
  position: absolute;
  top: 80%;
  left: 42%;
}
.backbtn {
  margin: 0;
  position: absolute;
  top: 80%;
  left: 20%;
}
</style>
</head>
<body>
    
    <div style="height: 8px; background:rgb(117, 115, 221);"> </div>

<nav class="navbar" role="navigation">
	<div class="container">
		<div class="navbar-header">
<div class="container container-table">
    <div class="row">
        <div class="bg-primary SystemInfo text-center" style="margin-top: 4px;">
        <img src='images/complaint_register.png' width=400;height=400;>
<!--         Complain Register Module -->
    	</div>
	</div>
</div>
		</div>
	</div>
</nav>

<div class="Line" style="height: 8px; background:rgb(117, 115, 221);"></div>

<center><img src="images/ERLY.jpg" style="margin-top: 5px;" width=100;height=100;></center><br>

<div class="container-fluid">

<div class="row">
	<div class="col-xs-offset-4 col-xs-4 roundborder" style="height: 350px;">
	
<div class="form text-center">
        <form action="" method="post" >
            <h2>Change Password</h2><br>
            <p class="msg" style='color:red;'> 
			<?php if($msg!=''){ print " $msg";} ?>
			
            <div class="form-group">
                <label for="username"> User Name :</label>
                <input type="text" id="username" name="username" placeholder="Enter a username" class= "form control" require>
            </div>  
</div>  
            <div class="form-group text-center">
                <label for="password"> Password :</label>
                <input type="password" name="password" placeholder="Enter your old password" class= "form control" require>
            </div> 
			
			<div class="form-group text-center">
                <label for="cnfpassword"> Confirm Old Password :</label>
                <input type="password" name="cnfpassword" placeholder="Confirm your old password" class= "form control" require>
            </div> 
			
			<div class="form-group text-center">
                <label for="newpassword"> New Password :</label>
                <input type="password" name="newpassword" placeholder="Enter your new password" class= "form control" require>
            </div> 
			
            <div class="center">
			<button class= "btn font-weight-bold" name="submit" style="border:1px solid #222; background:#80aaff; color:#fff: ">Change Now</button>
            <br><br>
			</div>
			
			<div class="backbtn">
			<button class= "btn font-weight-bold" name="backbtn" style="border:1px solid #222; background:#80aaff; color:#fff: ">Back</button>
            <br><br>
			</div>
			
        </form>
    </div>
</div>
</div>
<?php
include 'jsscript.php'; ?>
</body>
</html>