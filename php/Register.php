<?php 
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		//store info for new acct
		$fn = $_POST['firstname'];
		$ln = $_POST['lastname'];
		$em = $_POST['email'];
		$pw = $_POST['password'];
	}
	//connect to db
	include 'mysql_info_local.php';
	//include 'mysql_info.php';
	
	$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
	
	//Error checking
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	
	//run query to register new user
	if(!empty($_POST["register"]))
	{
		$sqlcommand = "INSERT INTO `accounts` (`UserID`, `FirstName`, `LastName`, `Email`, `Password`, `ImgURL`, `RoleID`) VALUES (NULL, '$fn', '$ln', '$em', '$pw', NULL, '0')";	//default to basic user
		$conn->query($sqlcommand);
		header("location:../login.php");	//send back to login
	}
?>