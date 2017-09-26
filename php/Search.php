<?php
	if($_SERVER["REQUEST_METHOD"] == "POST")	//gather task id from query
	{
		$taskid = $_POST['taskid'];
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
	
	$sqlcommand = "SELECT * FROM `tasks` where TaskID = $taskid";
	$dataset = $conn->query($sqlcommand);
	if($dataset->num_rows == 1)	//exact match
	{
		header("location:../ViewTask.php?tid=$taskid");
	}
	else if($dataset->num_rows > 0)
	{
		session_start();
		$_SESSION["SearchResults"] = $dataset;
		header("location:SearchResults.php");
	}
	else
	{
		header("location:../index.php");
	}
	
?>