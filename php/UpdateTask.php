<?php 
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		//store info to update task
		$tid = $_POST['taskid'];
		$tn = $_POST['taskname'];
		$td = $_POST['taskdescription'];
		$id = $_POST['assignee'];	//get from select dropdown
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
	
	//run query to register new user
	if(!empty($_POST["UpdateTask"]))
	{
		//$sqlcommand = "UPDATE `tasks` SET `TaskName` = '$tn', SET `TaskDesc` = '$td', SET `UserID` = $id WHERE `TaskID` = $tid";
		$sqlUpdateTaskName = "UPDATE tasks SET TaskName = '$tn' WHERE TaskID = $tid";
		$sqlUpdateTaskDesc = "UPDATE tasks SET TaskDesc = '$td' WHERE TaskID = $tid";
		$sqlUpdateTaskAssignee = "UPDATE tasks SET UserID = '$id' WHERE TaskID = $tid";
		$conn->query($sqlUpdateTaskName);
		$conn->query($sqlUpdateTaskDesc);
		$conn->query($sqlUpdateTaskAssignee);
		header("location:../ViewTask.php?tid=$tid");	//send back to task
	}	
?>