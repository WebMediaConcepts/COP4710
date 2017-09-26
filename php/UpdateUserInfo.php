<?php 
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		session_start();
		$id = $_SESSION["UserID"];
		//store info to update task
		$fn = $_POST['firstname'];
		$ln = $_POST['lastname'];
		$em = $_POST['email'];
		$pw = $_POST['password'];	
	}
	#region connect to db
	include 'mysql_info_local.php';
	//include 'mysql_info.php';
	
	$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
	
	//Error checking
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	#endregion
	
	//run query to update new user
	if(!empty($_POST["UpdateUserInfo"]))
	{
		$sqlUpdateFirstName = "UPDATE accounts SET FirstName = '$fn' WHERE UserID = $id";
		$sqlUpdateLastName = "UPDATE accounts SET LastName = '$ln' WHERE UserID = $id";
		$sqlUpdateEmail = "UPDATE accounts SET Email = '$em' WHERE UserID = $id";
		$sqlUpdatePassword = "UPDATE accounts SET Password = '$pw' WHERE UserID = $id";
		$conn->query($sqlUpdateFirstName);
		$conn->query($sqlUpdateLastName);
		$conn->query($sqlUpdateEmail);
		$conn->query($sqlUpdatePassword);
		$_SESSION["FirstName"] = $fn;
		$_SESSION["LastName"] = $ln;
		$_SESSION["Email"] = $em;
		$_SESSION["Password"] = $pw;
		header("location:../index.php");	//send back to task
	}	
?>