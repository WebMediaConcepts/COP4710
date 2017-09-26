<?php 
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		//store info for new task		
		$tn = $_POST['taskname'];
		$td = $_POST['taskdescription'];
		$id = $_POST['assignee'];	//get from select dropdown
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
	if(!empty($_POST["CreateTask"]))
	{
		$sqlcommand = "INSERT INTO `tasks` (`TaskID`, `TaskName`, `TaskDesc`, `Status`, `UserID`) VALUES (NULL, '$tn', '$td', '0', '$id')";
		$conn->query($sqlcommand);
		header("location:../index.php");	//send back to login
	}	
?>