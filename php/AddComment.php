<?php
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		//store info for new acct
		session_start();
		$uid = $_SESSION["UserID"];		//id of the commentor
		$comment = $_POST['comment'];
		$nid = $_POST['notifyassigneeID'];	//the user id of who we will notify of a new comment
		$tid = $_POST['taskid'];
		
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
	$sqlcommand = "INSERT INTO `comments` (`CommentID`, `UserID`, `Description`, `TaskID`, `CommentStatusTypeID`) VALUES (NULL, '$uid', '$comment', '$tid', '0')";	// 0 for open status type id
	$dataset = $conn->query($sqlcommand);
	
	$sqlAddNotificiation = "INSERT INTO `notifications` (`NotificationID`, `UserID`, `TaskID`, `NotificationTypeID`) VALUES (NULL, '$nid', '$tid', '0')";	//0 for new comment type notification
	$conn->query($sqlAddNotificiation);
	
	header("location:../ViewTask.php?tid=$tid");
?>