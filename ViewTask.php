<?php
session_start();
if($_SESSION['UserID']== '')	//not logged in
{
	header("location:login.php");
}
else
{
	//pageload
	$ActiveUserID = $_SESSION["UserID"];
	//connect to db
	include 'php/mysql_info_local.php';
	
	$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
	//get notifications counts
	$GetNotificationCount = "SELECT * FROM `notifications` WHERE UserID=$ActiveUserID";
	$NotificationsTotal = $conn->query($GetNotificationCount);
	if($NotificationsTotal->num_rows > 0)
	{
		$totalNotifications = $NotificationsTotal->num_rows;
	}
	else{
		$totalNotifications = "0";
	}
	//open tasks
	$GetOpenTasksCount = "SELECT * FROM `tasks` where UserID ='" . $ActiveUserID . "' AND status = 0";
	$OpenTasksTotal = $conn->query($GetOpenTasksCount);
	if($OpenTasksTotal->num_rows > 0)
	{
		$totalOpenTasks = $OpenTasksTotal->num_rows;
	}
	else{
		$totalOpenTasks = "0";
	}
	//closed tasks
	$GetClosedTasksCount = "SELECT * FROM `tasks` where UserID ='" . $ActiveUserID . "' AND status = 1";
	$ClosedTasksTotal = $conn->query($GetClosedTasksCount);
	if($ClosedTasksTotal->num_rows > 0)
	{
		$totalClosedTasks = $ClosedTasksTotal->num_rows;
	}
	else{
		$totalClosedTasks = "0";
	}
	//ready for testing
	
	//re opened tasks
	$GetReopenedTasksCount = "SELECT * FROM `tasks` where UserID ='" . $ActiveUserID . "' AND status = 3";
	$ReopenedTasksTotal = $conn->query($GetReopenedTasksCount);
	if($ReopenedTasksTotal->num_rows > 0)
	{
		$totalReopenedTasks = $ReopenedTasksTotal->num_rows;
	}
	else{
		$totalReopenedTasks = "0";
	}
}
if(!empty($_POST["logout"])) 	//clicked log out
{
	$_POST["login"] = '';
	session_destroy();
	header("location:login.php");
}
//gather id from query string, redirect if not
if($_SERVER["REQUEST_METHOD"] == "GET")
{
	if(isset($_GET['tid']))	//validate query string
	{
		$taskid = $_GET['tid'];
	}
	else
	{
		header("location:index.php");
	}
}
else
{
	header("location:index.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>COP4710 - Task Manager</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
	
    <!-- Custom CSS -->
	<link href="css/custom.css" rel="stylesheet">
	
	<!-- Font Awesome Icon CSS -->
	<link href="css/font-awesome.min.css" rel="stylesheet">
    <style>
    body {
        padding-top: 85px;
        /* Required padding for .navbar-fixed-top. Remove if using .navbar-static-top. Change if height of navigation changes. */
    }
    </style>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body id="top">
<script>
//javascript to prevent alphabetic & special chars input into task id search
	var isShift = false;
	function keyUP(keyCode) {
	   
		if (keyCode == 16)
			isShift = false;
	}
	function isNumeric(keyCode) {
		if (keyCode == 16)
			isShift = true;

		var isValidInput = ((keyCode >= 48 && keyCode <= 57 || keyCode == 8 ||
			  (keyCode >= 96 && keyCode <= 105) || keyCode == 13) && isShift == false);
		if (!isValidInput)
		{
			$('#divValidateSearch').removeClass('has-success');	//change color of input
			$('#divValidateSearch').addClass('has-error');
		}
		else {
			$('#divValidateSearch').removeClass('has-error');
			$('#divValidateSearch').addClass('has-success');
		}
		return isValidInput;
	}
</script>
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <?php
					if(isset($_SESSION["ImgURL"]) && $_SESSION["ImgURL"] != "")
					{
						$navProfilepic = $_SESSION["ImgURL"];
						$navFullName = $_SESSION["FirstName"]." ".$_SESSION["LastName"];
						echo "<a href=\"#\" title='Click to edit profile image' data-toggle=\"modal\" data-target=\"#ModalUploadProfilePic\">";
						echo "<img src='$navProfilepic'  class='img-circle' style='height: 65px' />";
						echo "</a>";
					}
					else	//display upload button
					{
						$defaultimg = "images/default.jpg";
						echo "<a href=\"#\" data-toggle=\"modal\" data-target=\"#ModalUploadProfilePic\">";
						echo "<img src='$defaultimg'  class='img-circle' style='height: 65px' />";
						echo "</a>";
					}
						//echo "<strong>$navFullName</strong>";
				?>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				<form class="navbar-form navbar-left" action="php/Search.php" method="post">
					<div id="divValidateSearch" class="form-group">
					  <input type="text" name="taskid" class="form-control" placeholder="Enter Task ID" onkeyup="keyUP(event.keyCode)" onkeydown="return isNumeric(event.keyCode);" onpaste="return false;">
					</div>
					<button type="submit" class="btn btn-default">Search</button>
				</form>
				<ul class="nav navbar-nav navbar-right">
					<li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-info"><?php if(isset($totalNotifications)) {echo $totalNotifications;} else {echo "0";} ?></span>&nbsp;<i class="fa fa-bell"></i>&nbsp;<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <?php
								//connect to db
								include 'php/mysql_info_local.php';
								//include 'php/mysql_info.php';
								
								$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
								
								//Error checking
								if($conn->connect_error)
								{
									die("Connection failed: " . $conn->connect_error);
								}
								$GetNotifications = "SELECT * FROM `notifications` WHERE UserID=$ActiveUserID";
								
								$NotificationsDs = $conn->query($GetNotifications);
								if($NotificationsDs->num_rows > 0)
								{
									while($notificiationRows = $NotificationsDs->fetch_assoc()) 	//get user info
									{
										$notificationTaskID = $notificiationRows["TaskID"];
										$notificationTypeID = $notificiationRows["NotificationTypeID"];	//type of notification
										if($notificationTypeID == 0)	//comment
										{
											echo "<li><a title='View Task $notificationTaskID' href='ViewTask.php?tid=$notificationTaskID'><i class='fa fa-comment'></i> New Comments!</a></li>";
										}
										else if ($notificationTypeID == 1)	//like
										{
											echo "<li><a title='View Task $notificationTaskID' href='ViewTask.php?tid=$notificationTaskID'><i class='fa fa-thumbs-o-up'></i> New Likes!</a></li>";
										}
									}
								}
							?>
                        </ul>
                    </li>
					<!--open count-->
					<li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-primary"><?php if(isset($totalOpenTasks)) {echo $totalOpenTasks;} else {echo "0";} ?></span>&nbsp;<i class="fa fa-circle-o"></i>&nbsp;<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <?php
								//connect to db
								include 'php/mysql_info_local.php';
								//include 'php/mysql_info.php';
								
								$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
								
								//Error checking
								if($conn->connect_error)
								{
									die("Connection failed: " . $conn->connect_error);
								}
								$OpenTasks = "SELECT * FROM `tasks` where UserID ='" . $ActiveUserID . "' AND status = 0";
								
								$OpenTasksDs = $conn->query($OpenTasks);
								if($OpenTasksDs->num_rows > 0)
								{
									while($OpenTasksRows = $OpenTasksDs->fetch_assoc()) 	//get user info
									{
										$OpenTasksTaskID = $OpenTasksRows["TaskID"];
										$OpenTasksTaskName = $OpenTasksRows["TaskName"];	//type of notification
										echo "<li><a title='View Task $OpenTasksTaskID' href='ViewTask.php?tid=$OpenTasksTaskID'>$OpenTasksTaskName</a></li>";
									}
								}
							?>
                        </ul>
                    </li>
					<!--closed count-->
					<li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-danger"><?php if(isset($totalClosedTasks)) {echo $totalClosedTasks;} else {echo "0";} ?></span>&nbsp;<i class="fa fa-ban"></i>&nbsp;<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <?php
								//connect to db
								include 'php/mysql_info_local.php';
								//include 'php/mysql_info.php';
								
								$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
								
								//Error checking
								if($conn->connect_error)
								{
									die("Connection failed: " . $conn->connect_error);
								}
								$ClosedTasks = "SELECT * FROM `tasks` where UserID ='" . $ActiveUserID . "' AND status = 1";
								
								$ClosedTasksDs = $conn->query($ClosedTasks);
								if($ClosedTasksDs->num_rows > 0)
								{
									while($ClosedTasksRows = $ClosedTasksDs->fetch_assoc()) 	//get user info
									{
										$ClosedTasksTaskID = $ClosedTasksRows["TaskID"];
										$ClosedTasksTaskName = $ClosedTasksRows["TaskName"];	//type of notification
										echo "<li><a title='View Task $ClosedTasksTaskID' href='ViewTask.php?tid=$ClosedTasksTaskID'>$ClosedTasksTaskName</a></li>";
									}
								}
							?>
                        </ul>
                    </li>
					<!--reopened count-->
					<li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-success"><?php if(isset($totalReopenedTasks)) {echo $totalReopenedTasks;} else {echo "0";} ?></span>&nbsp;<i class="fa fa-repeat"></i>&nbsp;<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <?php
								//connect to db
								include 'php/mysql_info_local.php';
								//include 'php/mysql_info.php';
								
								$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
								
								//Error checking
								if($conn->connect_error)
								{
									die("Connection failed: " . $conn->connect_error);
								}
								$ReopenedTasks = "SELECT * FROM `tasks` where UserID ='" . $ActiveUserID . "' AND status = 3";
								
								$ReopenedTasksDs = $conn->query($ReopenedTasks);
								if($ReopenedTasksDs->num_rows > 0)
								{
									while($ReopenedTasksRows = $ReopenedTasksDs->fetch_assoc()) 	//get user info
									{
										$ReopenedTasksTaskID = $ReopenedTasksRows["TaskID"];
										$ReopenedTasksTaskName = $ReopenedTasksRows["TaskName"];	//type of notification
										echo "<li><a title='View Task $ReopenedTasksTaskID' href='ViewTask.php?tid=$ReopenedTasksTaskID'>$ReopenedTasksTaskName</a></li>";
									}
								}
							?>
                        </ul>
                    </li>
					<li>
						<a href="index.php">HOME</a>
					</li>
                    <li>
                        <form action="" method="post" id="frmLogout" style="padding-top: 17.5px;">
							<input class="btn btn-link btn-sm" type="submit" name="logout" value="Logout">
						</form>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
    <!-- Page Content -->
    <div class="content-wrapper">
        <div class="row">
			<div class="col-sm-12">
				<h2 class="page-header"><i class="fa fa-folder-open"></i> Task ID: <?php echo "$taskid"; ?></h3>
			</div>
			<div class='col-sm-12'>
            <?php
			//connect to db
			include 'php/mysql_info_local.php';
			//include 'php/mysql_info.php';
			
			$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
			
			//Error checking
			if($conn->connect_error)
			{
				die("Connection failed: " . $conn->connect_error);
			}
			$sqlcommand = "SELECT * FROM `tasks` where TaskID = $taskid";
			$dataset = $conn->query($sqlcommand);
			if($dataset->num_rows > 0)	//successfully logged in / valid query
			{
				while($row = $dataset->fetch_assoc()) 	//get user info
				{
					$taskid = $row['TaskID'];
					$taskname = $row['TaskName'];
					$taskdescription = $row['TaskDesc'];
					$taskstatus = $row['Status'];
					$assigneeID = $row['UserID'];
					$sqlcommand2 = "SELECT * FROM `accounts` where UserID = $assigneeID";
					$dataset2 = $conn->query($sqlcommand2);
					if($dataset2->num_rows > 0)
					{
						while($row2 = $dataset2->fetch_assoc())
						{
							$AssignedTo = $row2["FirstName"] . " " . $row2["LastName"];
						}
					}
					
					echo "<div class='row'>";
					echo "<div class='col-sm-10'>";
					echo "<h3><b>Task Name:&nbsp</b>$taskname&nbsp;";
					if($taskstatus == 0)	//open
					{
						echo "<span class='label label-primary'>open</span>";
					}
					else if($taskstatus == 1)//closed
					{
						echo "<span class='label label-danger'>closed</span>";
					}
					else if($taskstatus == 3)//closed
					{
						echo "<span class='label label-success'>reopened</span>";
					}
					echo "</h3>";
					echo "<strong>Assigned To: </strong>$AssignedTo";
					echo "<p class='lead'>$taskdescription</p>";
					//echo "<strong>Status: </strong>$taskstatus";
					
					echo "</div>";//./col-sm-10
					echo "<div class='col-sm-2'>";
					//echo "<a class='btn btn-default btn-xs btn-block' href='/EditTask.php?tid=$taskid' id='$taskid'> <small>Edit Task</small></a>";
					if($taskstatus == 0 || $taskstatus == 3)
					{
						echo "<div class='row'>";
							echo "<div class='col-sm-12 col-xs-6'>";
								echo "<a class='btn btn-default btn-block' href='#ModalUpdateTask' data-toggle='modal'><i class='fa fa-pencil'></i> Edit Task</a>";
							echo "</div>";
							echo "<div class='col-sm-12 col-xs-6'>";
								echo "<a class='btn btn-danger btn-block' href='php/CloseTask.php?tid=$taskid' id='$taskid'><i class='fa fa-times'></i> Close</a>";
							echo "</div>";
						echo "</div>";
					}
					else  if($taskstatus == 1) //closed
					{
						echo "<div class='row'>";
							echo "<div class='col-sm-12 col-xs-6'>";
								echo "<a class='btn btn-success btn-block' href='php/CloseTask.php?tid=$taskid&cmd=reopen' id='$taskid'><i class='fa fa-folder-open-o'></i> Reopen</a>";
							echo "</div>";
						echo "</div>";
					}
					echo "</div>";//./col-sm-2
					echo "</div>";//./row
					echo "<hr>";
				}									
			}
			else 
			{
				$taskmessage = "<div class=\"alert alert-danger\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>Task Not Found!</alert>";
				echo "<div class=\"row\">";
				echo "<div class=\"col-lg-6\">";
				echo "$taskmessage";
				echo "</div>";
				echo "</div>";
			}
			?>
			</div>			
        </div>
        <!-- /.row -->
		<!--comments-->
		<div id="comment-container">

<style>
.thumbnail {
    padding:0px;
}
.panel {
	position:relative;
}
.panel>.panel-heading:after,.panel>.panel-heading:before{
	position:absolute;
	top:11px;left:-16px;
	right:100%;
	width:0;
	height:0;
	display:block;
	content:" ";
	border-color:transparent;
	border-style:solid solid outset;
	pointer-events:none;
}
.panel>.panel-heading:after{
	border-width:7px;
	border-right-color:#f7f7f7;
	margin-top:1px;
	margin-left:2px;
}
.panel>.panel-heading:before{
	border-right-color:#ddd;
	border-width:8px;
}
</style>

<?php
			//connect to db
			include 'php/mysql_info_local.php';
			//include 'php/mysql_info.php';
			
			$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
			
			//Error checking
			if($conn->connect_error)
			{
				die("Connection failed: " . $conn->connect_error);
			}
			$sqlcommand = "SELECT * FROM `comments` where TaskID = ".$taskid." AND CommentStatusTypeID = 0";	//open unedited comments
			$dataset = $conn->query($sqlcommand);
			if($dataset->num_rows > 0)	//successfully logged in / valid query
			{
				while($row = $dataset->fetch_assoc()) 	//get user info
				{
					$comment = $row["Description"];
					$commentorID = $row['UserID'];
					$commentID = $row['CommentID'];
					$sqlcommand2 = "SELECT * FROM `accounts` where UserID = $commentorID";
					$dataset2 = $conn->query($sqlcommand2);
					if($dataset2->num_rows > 0)
					{
						while($row2 = $dataset2->fetch_assoc())
						{
							$commentor = $row2["FirstName"] . " " . $row2["LastName"];
						if($row2["ImgURL"] == "" || $row2["ImgURL"] == NULL)
							{
								$commentorPic = "images/default.jpg";
							}
							else{
								$commentorPic = $row2["ImgURL"];
							}
						}
					}
					
					echo "<div class='row'>";
					echo "<div class='col-sm-1 col-xs-3'>";
					echo "<div class='thumbnail'>";
						//user image here
					echo "<img class='img-responsive user-photo' src='$commentorPic'>";
					echo "</div>";
					echo "</div>";//./col-sm-1
					echo "<div class='col-sm-5 col-xs-9'>";
					echo "<div class='panel panel-default'>";
					echo "<div class='panel-heading'>";
					//username here
					echo "<strong><i class='fa fa-user'></i> $commentor</strong>";
					echo "<ul class='list-inline pull-right'>";
					if($commentorID == $ActiveUserID)	//only show delete for Active Users own comments
					{
						echo "<li><a class=\"text-danger\" href=\"php/UpdateComment.php?cmd=delete&&uid=$commentorID&tid=$taskid&cid=$commentID\"><i class='fa fa-times'></i> Delete</a></li>";
					}
					echo "<li><a class=\"text-primary\" href=\"php/UpdateComment.php?cmd=like&uid=$commentorID&tid=$taskid&cid=$commentID\"><i class='fa fa-thumbs-o-up'></i> like</a></li>";
					echo "</ul>";
					echo "</div>";//./heading
					echo "<div class='panel-body'>";
					//comment here
					echo "<strong>$comment</strong>";
					echo "</div>";//./body
					echo "</div>";//./panel
					echo "</div>";//./col-sm-5
					echo "</div>";//./row
				}									
			}
			else 
			{
				$commentMessage = "<div class='alert alert-info'><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>Be the first to leave a comment!</alert>";
				echo "<div class=\"row\">";
				echo "<div class=\"col-lg-6\">";
				echo "$commentMessage";
				echo "</div>";
				echo "</div>";
			}
			?>

</div><!-- /container -->
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-8">
			<form id="CommentForm" class="form-group" method="post" action="php/AddComment.php">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label for="comment">Leave a Comment <i class="fa fa-comment"></i></label>
							<textarea type="text" class="form-control" rows="4" name="comment" placeholder="Leave a comment"> </textarea>
							<span id="comment_err"></span>
						</div>
					</div>
					<!--hidden fields for notificiations-->
					<input name="notifyassigneeID" type="hidden" value="<?php echo $assigneeID; ?>" />
					<input name="taskid" type="hidden" value="<?php echo $taskid; ?>" />
				</div>
				<br>
				<div class="row">
					<div class="col-md-12 clearfix">
					   <input type="submit" name="PostComment" value="Post" class="btn btn-info">
					</div>
				</div>
			</form>
				
			</div>
		</div>
		<a href="#top" id="backToTop" title="Go to top"><i class='fa fa-chevron-up'></i></a>
    </div>
	<!-- /.content-wrapper -->
	<div class="footer">
		<ul class="list-inline">	
			<li>
				<b>User ID: </b><?php echo $_SESSION["UserID"]; ?>
			</li>
			<li>
				<b>User: </b><?php echo $_SESSION["FirstName"]. " " .$_SESSION["LastName"] ; ?>
			</li>
		</ul>

	</div>
<script>
	// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("backToTop").style.display = "block";
    } else {
        document.getElementById("backToTop").style.display = "none";
    }
}

// When the user clicks on the button, scroll to the top of the document
/* function topFunction() {
    document.body.scrollTop = 0; // For Chrome, Safari and Opera 
    document.documentElement.scrollTop = 0; // For IE and Firefox
} */

$(document).on('click', '#backToTop', function (event) {
	event.preventDefault();

	$('html, body').animate({
		scrollTop: $($.attr(this, 'href')).offset().top
	}, 500);
});
</script>
    <!-- jQuery Version 1.11.1 -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>
<!-- ModalUpdateTask -->
    <div id="ModalUpdateTask" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit Task</h4>
                </div>
                <div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<form id="UpdateTask" class="form-group" method="post" action="php/UpdateTask.php">
							<input type="hidden" name="taskid" value="<?php echo $taskid; ?>" />
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="taskname">Task Name</label>
										<input id="tname" type="text" class="form-control" name="taskname" placeholder="Enter The Task Name" value="<?php echo $taskname; ?>" />
										<span id="tname_err"></span>
									</div>
									
								</div>
								<div class="col-md-6">
									<label>Assign To</label>
									<select class="form-control" name="assignee">		<!-- Want to run a query that enter nurse name in drop down-->
										<?php
											$sqlAssignee = "select * FROM `accounts` where UserID=$assigneeID";
											$resultAssignee=$conn->query($sqlAssignee);
											if($resultAssignee->num_rows > 0)	//successfully logged in
											{
												while($rowAssignee = $resultAssignee->fetch_assoc())
												{
													$assigneeUserID = $rowAssignee['UserID'];
													$assigneeFirstname = $rowAssignee['FirstName'];
													$assigneeLastname = $rowAssignee['LastName'];
												}
											}
											$sql = 'SELECT * FROM `accounts`';
											$result=$conn->query($sql);
											if($result->num_rows > 0)	//successfully logged in
											{
												echo '<option value='.$assigneeUserID.'>'.$assigneeFirstname." ".$assigneeLastname.'</option>';
												while($row = $result->fetch_assoc())
												{
													$userid = $row['UserID'];
													$firstname = $row['FirstName'];
													$lastname = $row['LastName'];
													echo '<option value='.$userid.'>'.$firstname." ".$lastname.'</option>';
												}
											}
										?>
										<span id="assignee_err"></span>
									</select>
								</div>
								<br>
								<div class="col-md-12">
									<div class="form-group">
										<label for="taskdesc">Description</label>
										<textarea type="text" class="form-control" name="taskdescription" placeholder="Enter The Description"><?php echo $taskdescription; ?></textarea>
										<span id="taskdesc_err"></span>
									</div>
								</div>
								
							</div>
							<br>
							<div class="row">
								<div class="col-md-12 clearfix">
								   <input type="submit" name="UpdateTask" value="Update Task" class="btn btn-primary btn-block">
								</div>
							</div>
						</form>
					</div>
				</div>
                    
                </div>
            </div>
        </div>
    </div>
</body>

</html>
