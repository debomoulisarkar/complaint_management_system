<!DOCTYPE html>
<html lang="en">
<head>
  <title>Complaint Register</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="css/mystyle.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">   
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> 
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>

<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header"> </div>
    <div class="container container-table">
    <div class="row">
        <div class="bg-primary WebInfo text-center" style="margin-top: 4px;">
			<img style="margin-top: -5px;" src="images/Complaint_register.jpeg" width=80;height=80;>
			<img src='images/complaint_register.png' width=350;height=350;>
			<!-- Helpdesk Complain Register Module -->
        </div>
    </div>
    </div>
  </div>
</nav>

<div class="container-fluid"> 
  <div class="row">
    
    <div class="col-sm-2 sidenavl text-center">
      <div class="row smheader text-warning"> Useful Links... </div><br>   
      <button type="button" class="btn btn-primary btn-md">
    <a href="https://er.indianrailways.gov.in/" style="color:white" target="_blank">Eastern Railway</a>
      </button><br><br><br><br>
      <button type="button" class="btn btn-primary btn-md">
    <a href="https://www.indianrailways.gov.in/" style="color:white" target="_blank">Indian Railways</a>
      </button><br><br><br><br>
      <button type="button" class="btn btn-primary btn-md">
      <p><a href="Login.php" style="color:white"><span class="glyphicon glyphicon-log-in"></span> Login</a></p>
      </button><br><br><br><br>
	  <button type="button" class="btn btn-primary btn-md">
      <p><a href="Register.php" style="color:white"><span class="glyphicon glyphicon-list-alt"></span> Register</a></p>
      </button><br><br><br>
    </div>

    <div class="col-sm-8 back-banner">
      <div class="row">
        <img style="margin-left: 15px; float:left;" class="img-circle" 
             src="images/IR150.jpg" width=80px; height=80px;>
        <img style="float:right;" 
             src="images/Ashoka.png" width=80px;height=80px;>
      </div>
      
      <div class="row text-justify DtlInfo back-image" 
           style="margin-top: 5px; margin-left: 5px; margin-right: 5px;"> 
      <br><br>
      <h1>Welcome,</h1>
      <p>This is a site for entering data of Complaint Register. Railway Department users can register their complaints through this application and forward it to the officer concerned for taking immediate action.
        Officers can ascertain the issues and after examinig them, can send it to the Admin users for assigning engineers for deffective solutions. Prioblems are categorised as Hardware, software and Other etc. 
        If the issues are not described clearly or the attachments are not related to the subject, then the Ofiicer can return the complaints to the users for re-submitting them. Attachments are generally advised to 
        make in .pdf / .jpg / .png format. Officer forwards the issue to the admin with necessary remarks and a movement register is kept herewith for easy tracking of the complaints.</p>
      <hr>
      </div>
    </div>

    <div class="col-sm-2 sidenavr">
      <div class="well text-center mmheader" style="background-color: #ceecef">
        <img class="img-rounded" style="margin-top: -10px;" src="images/Execute.png" width=100%;height=100%;>
        <br>L2-Officers
      </div>
      <div class="well text-center mmheader" style="background-color: #ceecef">
        <img class="img-rounded" style="margin-top: -10px;" src="images/Execute2.png" width=100%;height=100%;>
        <br>L1-Users
      </div>
      <div class="well text-center mmheader" style="background-color: #ceecef">
        <img class="img-rounded" style="margin-top: -10px;" src="images/Execute3.jpg" width=100%;height=100%;>
        <br>Admins
	</div>
    </div>
  </div>
 </div>  
<br><br><br><br><br><br><br>
<?php
include("include/footer.php");
 ?>
</body>
</html>