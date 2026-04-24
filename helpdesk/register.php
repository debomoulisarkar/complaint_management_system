<?php
include("connection.php");
include("jsscript.php");

$msg=' '; 

if(isset($_POST['submit'])) 
	{
		$l_name	= $_POST['name'];
		$l_email = $_POST['email'];
        $l_empid = $_POST['id'];
        $l_username = $_POST['username'];
		$l_password = $_POST['password'];
		$l_cnfpassword = $_POST['cnfpassword'];
		$l_phone = $_POST['phone'];
		$l_department = $_POST['department'];
        $l_designation= $_POST['designation'];
		
		if ($l_password==$l_cnfpassword) {

        $select1="SELECT * FROM user WHERE department= '$l_department' AND id= '$l_empid'";
        $select_user= mysqli_query($conn,$select1);
		
        if (mysqli_num_rows($select_user)>0){
			echo ("<SCRIPT LANGUAGE='JavaScript'>
			window.alert('user already exists!')			
			</SCRIPT>");
        }
        else{
            $insert1="INSERT INTO user (ename, email, id, user_name, pwd, phone, department, designation,r_date) 
			VALUES ('$l_name', '$l_email', '$l_empid','$l_username','$l_password','$l_phone', '$l_department','$l_designation',now())";
			$insert= mysqli_query($conn,$insert1);
            
            echo ("<SCRIPT LANGUAGE='JavaScript'>
			window.alert('Successfully Inserted')			
			</SCRIPT>");
			
            
			
		if ($l_designation=='OS' || $l_designation=='AA'){
            $l_user='User';
            $table_insrt ="UPDATE user SET user_type='$l_user' WHERE id='$l_empid' AND pwd='$l_password'";
            $table=mysqli_query($conn,$table_insrt);
			}
        elseif($l_designation=='APO'  || $l_designation=='AFA'){
            $l_user='Officer';
            $table_insrt ="UPDATE user SET user_type='$l_user' WHERE id='$l_empid' AND pwd='$l_password'";
            $table=mysqli_query($conn,$table_insrt);
			}
/*			$select2="SELECT user_type FROM userinfo WHERE department= '$l_department' AND designation= '$l_designation'";
			$sqlrst= mysqli_query($conn,$select2);
			$user_type=$sqlrst[0];
			$table_upd ="UPDATE user SET user_type='$user_type' WHERE username='$l_username' AND designation= '$l_designation'";
			$table=mysqli_query($conn,$table_upd); */
			
        }

	}
	else {
		echo ("<SCRIPT LANGUAGE='JavaScript'>
			window.alert('Password does not match!')			
			</SCRIPT>");}

		header('location:login.php');
        exit;
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial scale=1.0">
    <title>document</title>
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

	<div class="col-xs-offset-4 col-xs-4 roundborder">
		<br>

    <div class="form">
        <form action="" method="post">
            <h2>Registration</h2>
            <br><br>
            <p class="msg" style='color:red;'> 
			<?php if($msg!=''){ print " $msg";} ?>
			
            <div class="form-group">
                <label for="">Name</label>
                <input type="text" name="name" placeholder="Enter your name" class= "form control" required>
            </div>
            <div class="form-group">
                <label for="">Email</label>
                <input type="email" name="email" placeholder="Enter your email" class= "form control" required>
            </div>
            <div class="form-group">
                <label for="">ID</label>
                <input type="text" name="id" placeholder="Enter your employee id" class= "form control" required>
            </div>
            <div class="form-group">
                <label for="">UserName</label>
                <input type="text" name="username" placeholder="Enter a username" class= "form control" required>
            </div>  
            <div class="form-group">
                <label for="">Password</label>
                <input type="password" name="password" placeholder="Enter your password" class= "form control" required>
            </div>    
            <div class="form-group">
                <label for="">Confirm Password</label>
                <input type="password" name="cnfpassword" placeholder="Confirm password" class= "form control" required>
            </div>
            <div class="form-group">
                <label for="">Phone</label>
                <input type="text" name="phone" placeholder="Enter your phone number" class= "form control" required>
            </div>
            <div class="form group" style="width: 400px; background:rgb(117, 115, 221);">
                <label for="">Department</label>
                <select name="department" id="" class="form-control">
                    <option value="Establishment">Establishment</option>
                    <option value="Mechanical">Mechanical</option>
                    <option value="Electrical">Electrical</option>
                    <option value="Personnel">Personnel</option>

                </select>
            </div>
            <div class="form group" style="width: 400px; background:rgb(117, 115, 221);">
                <label for="">Designation</label>
                <select name="designation" id="" class="form-control">
                    <option value="OS">OS</option>
                    <option value="AFA">AFA</option>
                    <option value="AA">AA</option>
                    <option value="APO">APO</option>
                </select>
            </div>
            <br><br>
            <button class= "btn font-weight-bold" name="submit" style="border:1px solid #222; background:#80aaff; color:#21130d: ">Register Now</button>
              <a href="index.php" class="btn font-weight-bold" style="border:1px solid #222; background:#80aaff; color:#21130d; margin-right: 10px;">Back to Home</a>
            <br><br>
            <p>Already have an account? <a href="login.php">Login Now</a></p>
        </form>
    </div>
</div>
</div>
</div>
</body>
</html>