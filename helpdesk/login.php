<?php
include("connection.php");
session_start();

$msg = '';

if (isset($_POST['submit'])) {


    $l_username = $_POST['username'];
    $l_password = $_POST['password'];

    $select1 = "SELECT * FROM user WHERE user_name = '$l_username' AND pwd = '$l_password'";
    $select_user = mysqli_query($conn, $select1);

    if (mysqli_num_rows($select_user) > 0) {
        $row1 = mysqli_fetch_assoc($select_user);

        if ($row1['user_type'] == 'User'){
            $_SESSION['user'] = $row1['user_name'];
            $_SESSION['id'] = $row1['id'];
            header('Location: user.php');
            exit;
        } 
        elseif ($row1['user_type'] == 'Senior Admin') {
            $_SESSION['admin'] = $row1['user_name'];
            $_SESSION['admin_type'] = $row1['A0'];
            header('Location: admin0.php');
            exit;
        }
        elseif ($row1['user_type'] == 'Officer') {
            $_SESSION['officer'] = $row1['user_name'];
            $_SESSION['id'] = $row1['id'];
            header('Location: officer.php');
            exit;
        }
        elseif ($row1['user_type'] == 'Admin') {
            $_SESSION['admin'] = $row1['user_name'];
            $_SESSION['admin_type'] != $row1['A0'];
            header('Location: admins.php');
            exit;
        } 
   
}
 else {
        $msg = "Incorrect username or password!"; 
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial scale=1.0">
    <title>LOGIN</title>
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
  top: 60%;
  left: 42%;
}
.center-msg {
  margin: 0;
  position: absolute;
  top: 85%;
  left: 42%;
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
            <h2>Login</h2><br>
            <p class="msg" style='color:red;'> 
			<?php if($msg!=''){ print " $msg";} ?>
            <div class="form-group">
                <label for="username"> User Name :</label>
                <input type="text" id="username" name="username" placeholder="Enter a username" class= "form control" required>
            </div>  
</div>  
            <div class="form-group text-center">
                <label for="password"> Password :</label>
                <input type="password" name="password" placeholder="Enter your password" class= "form control" require>
            </div> 
			
            <div class="center">
  <button class="btn font-weight-bold" name="submit" style="border:1px solid #222; background:#80aaff; color:#fff; margin-right: 10px;">Login Now</button>
  <a href="index.php" class="btn font-weight-bold" style="border:1px solid #222; background:#80aaff; color:#fff; margin-right: 10px;">Back to Home</a>
  <br><br>
</div>
			<div class="center-msg">
            <p>Don't have an account? <a href="register.php">Register Now</a></p>
			</div>
        </form>
    </div>
</div>
</div>
  
</body>
</html>