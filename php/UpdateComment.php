<?php
	if($_SERVER["REQUEST_METHOD"] == "GET")	//gather task id from query
	{
		if(isset($_GET['tid']) && isset($_GET['cid']) && isset($_GET['uid']))	//validate query string
		{
			$notifyUserID = $_GET['uid'];
			$taskID = $_GET['tid'];
			$commentID = $_GET['cid'];
		}
		else
		{
			header("location:../index.php");
		}
		if($_GET['cmd'] == "like")
		{
			$NotificationTypeID = 1;	//likes
			$CommentStatusTypeID = 0; //open/visible comment
		}
		else if($_GET['cmd'] == "delete")
		{
			$NotificationTypeID = 2;	//deleted comment
			$CommentStatusTypeID = 1; //closed/hidden comment
		}
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
	
	//run query to update comment to closed
	$sqlcommand = "UPDATE comments SET CommentStatusTypeID = $CommentStatusTypeID WHERE CommentID = $commentID";
	$dataset = $conn->query($sqlcommand);
	
	
	$sqlAddNotificiation = "INSERT INTO `notifications` (`NotificationID`, `UserID`, `TaskID`, `NotificationTypeID`) VALUES (NULL, '$notifyUserID', '$taskID', '$NotificationTypeID')";	
	$conn->query($sqlAddNotificiation);
	
	header("location:../ViewTask.php?tid=$taskID");
?>