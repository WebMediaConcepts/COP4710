<?php
	if($_SERVER["REQUEST_METHOD"] == "GET")	//gather task id from query
	{
		$taskid = $_GET['tid'];
	}
	if(isset($_GET['cmd']))	//validate query string
	{
		if($_GET['cmd'] == "reopen")
		{
			$ReOpenBit = 3;
		}
	}
	else
	{
		$ReOpenBit = 1;
	}
	
	#region connect to db
	
	//connect to db
	include 'mysql_info_local.php';
	//include 'mysql_info.php';
	
	$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
	//Error checking
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	#endregion
	
	//run query to update task to closed
	$sqlcommand = "UPDATE tasks SET Status = $ReOpenBit WHERE TaskID = $taskid";
	
	$dataset = $conn->query($sqlcommand);
	header("location:../index.php");
?>