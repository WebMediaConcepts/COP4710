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
    <div id="PageContent" class="content-wrapper">
		
	<div class="row">
		
		<div class="col-sm-2 hidden">
		<?php
			if(isset($_SESSION["ImgURL"]) && $_SESSION["ImgURL"] != "")
			{
				$profilepic = $_SESSION["ImgURL"];
				echo "<a title='Click to update profile information' data-toggle=\"modal\" data-target=\"#ModalUpdateUserInfo\" class=\"btn btn-default btn-xs pull-right\"><i class='fa fa-pencil-square-o'></i></a>";
				echo "<a title='Click to edit profile image' data-toggle=\"modal\" data-target=\"#ModalUploadProfilePic\">";
				echo "<img src='$profilepic'  class='img-responsive img-circle center-block' style='width: 125px' />";
				echo "</a>";
			}
			else	//display upload button
			{
				$defaultimg = "images/default.jpg";
				echo "<a data-toggle=\"modal\" data-target=\"#ModalUpdateUserInfo\" class=\"btn btn-danger btn-xs pull-right\"><i class='fa fa-pencil-square-o'></i></a>";
				echo "<a data-toggle=\"modal\" data-target=\"#ModalUploadProfilePic\">";
				echo "<img src='$defaultimg'  class='img-responsive img-circle center-block' style='width: 125px' />";
				echo "</a>";
			}
		?>
		</div>
		<div class="col-sm-12">
			<h3 class="page-header">
			Welcome, <?php echo ucfirst($_SESSION["FirstName"]); ?><br class="hidden-lg hidden-md hidden-sm" />
			<a data-toggle="collapse" href="#collapseUpdateUserInfo" aria-expanded="false" aria-controls="collapseUpdateUserInfo" class="btn btn-default"><i class='fa fa-user'></i><span class="hidden-xs"> Update Profile</span></a>
			<a data-toggle="collapse" href="#collapseCreateTask" aria-expanded="false" aria-controls="collapseCreateTask" class="btn btn-primary"><i class='fa fa-pencil'></i><span class="hidden-xs"> Create New Task</span></a>

			</h3>
			
			
		</div>
	</div>
        <!-- /.row -->
	<div class="row">
		<div class="col-lg-6">
		<div class="collapse" id="collapseCreateTask">
			  <div class="well">
				<div class="row">
						<div class="col-md-12">
							<div class="error-message"><?php if(isset($message)) { echo $message; } ?></div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<form id="RegForm" class="form-group" method="post" action="php/CreateTask.php">
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label for="firstname">Task Name</label>
											<input id="tname" type="text" class="form-control" name="taskname" placeholder="Enter The Task Name" />
											<span id="tname_err"></span>
										</div>
										
									</div>
									<div class="col-md-6">
										<label>Assign To</label>
										<select class="form-control" name="assignee">		<!-- Want to run a query that enter nurse name in drop down-->
											<?php
												$sql = 'SELECT * FROM `accounts`';
												$result=$conn->query($sql);
												if($result->num_rows > 0)	//successfully logged in
												{
													echo '<option value="33">---Select Assignee---</option>';
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
											<textarea id="taskdesc" type="text" class="form-control" name="taskdescription" placeholder="Enter The Description"> </textarea>
											<span id="taskdesc_err"></span>
										</div>
									</div>
									
								</div>
								<br>
								<div class="row">
									<div class="col-md-12 clearfix">
									   <input type="submit" name="CreateTask" value="Create Task" class="btn btn-primary btn-block">
									</div>
								</div>
							</form>
						</div>
					</div>
			  </div>
			</div>
		<div class="collapse" id="collapseUpdateUserInfo">
			  <div class="well">
					<div class="row">
					<div class="col-md-12">
						<form id="UpdateUserForm" class="form-group" method="post" action="php/UpdateUserInfo.php">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname">First Name</label>
                                    <input id="fname" type="text" class="form-control" name="firstname" value="<?php echo $_SESSION["FirstName"]; ?>" />
                                    <span id="fname_err"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">Last Name</label>
                                    <input type="text" class="form-control" name="lastname" value="<?php echo $_SESSION["LastName"]; ?>" />
                                    <span id="lname_err"></span>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
							<div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="text" class="form-control" name="email" value="<?php echo $_SESSION["Email"]; ?>" />
                                    <span id="em_err"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" value="<?php echo $_SESSION["Password"]; ?>" />
                                    <span id="pw_err"></span>
                                </div>
                            </div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12 clearfix">
                               <input type="submit" name="UpdateUserInfo" value="Update User Info" class="btn btn-danger btn-block">
							   <!--	<a data-toggle="modal" data-target="#ModalUploadProfilePic" class="btn btn-link btn-sm btn-block">Change Profile Pic</a>-->
                            </div>
						</div>
                    </form>
					</div>
				</div>
			  </div>
			</div>
		
						<div id="panelOpenTasks">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-10">
							<strong class="lead"><?php echo ucfirst($_SESSION["FirstName"]); ?>'s open tasks</strong>
						</div>
						<div class="col-xs-2">
							<a id="btnCollapseOpenTasks" data-toggle="collapse" href="#CollapseOpenTasks" aria-expanded="false" aria-controls="CollapseOpenTasks" class="btn btn-default pull-right" onclick="$(this).find('i').toggleClass('fa-minus fa-plus');"><i class="fa fa-minus"></i></a>
						</div>
					</div>
				</div>
				<div class="panel-body collapse in" id="CollapseOpenTasks" style="max-height: 300px; overflow-y: scroll;">
					<div class="row">
						<div class="col-md-12">
							<?php
								include 'php/mysql_info_local.php';
								//include 'php/mysql_info.php';	
									
								$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
								
								//Error checking
								if($conn->connect_error)
								{
									die("Connection failed: " . $conn->connect_error);
								}
								$sqlcommand = "SELECT * FROM `tasks` where UserID ='" . $_SESSION["UserID"] . "' AND status = 0";
								$dataset = $conn->query($sqlcommand);
								if($dataset->num_rows > 0)	//successfully logged in / valid query
								{
									$totalOpenTasks = $dataset->num_rows;
									while($row = $dataset->fetch_assoc()) 	//get user info
									{
										$taskid = $row['TaskID'];
										$taskname = $row['TaskName'];
										$taskdescription = $row['TaskDesc'];
										$taskstatus = $row['Status'];
										echo "<div class='row'>";
										echo "<div class='col-lg-10 col-md-9 col-sm-8'>";
										
										echo "<a href='ViewTask.php?tid=$taskid' id='$taskid'>";
										echo "<b>ID: </b>$taskid";
										echo "<p>$taskname&nbsp;";
										if($taskstatus == 0)	//open
										{
											echo "<span class='label label-primary'>open</span>";
										}
										else if($taskstatus == 1)//closed
										{
											echo "<span class='label label-danger'>closed</span>";
										}
										else if($taskstatus == 3)//reopened
										{
											echo "<span class='label label-success'>reopened</span>";
										}
										echo "</p>";
										echo "</a>";
										echo "<strong>$taskdescription</strong>";
										echo "</div>";//./col
										echo "<div class='col-lg-2 col-md-3 col-sm-4'>";
										echo "<div class='row'>";
										echo "<div class='col-sm-12 col-xs-6'>";
										echo "<a class='btn btn-default btn-sm btn-block' href='ViewTask.php?tid=$taskid' id='$taskid'> <small>View</small></a>";
										echo "</div>";
										echo "<div class='col-sm-12 col-xs-6'>";
										echo "<a class='btn btn-danger btn-sm btn-block' href='php/CloseTask.php?tid=$taskid' id='$taskid'> <small>Close</small></a>";
										echo "</div>";
										echo "</div>";//./row
										echo "</div>";//./col-sm-2
										echo "</div>";//./row
										//echo "<strong>status: </strong>$taskstatus ";
										//so when you click the below link, CloseTask.php?id=`$taskid` runs
										echo "<hr>";
									}									
								}
								else 
								{
									$taskmessage = "You're all done!";
									echo "$taskmessage";
								}
							?>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-sm-12 col-xs-12">
							<i>Total Number of Open Tasks: </i><span class="label label-primary"><?php if(isset($totalOpenTasks)) {echo $totalOpenTasks;} else {echo "0";} ?></span>
						</div>
						<div class="col-sm-12 col-xs-12">
							<a data-toggle="modal" data-target="#ModalCreateTask" class="btn btn-primary btn-block btn-sm hidden"><i class='fa fa-pencil'></i><span class="hidden-xs"> New</span></a>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
		<div class="col-lg-6">

			<div id="panelClosedTasks">
				<!--closed tasks grid-->
			<div class="panel panel-danger">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-10">
							<strong class="lead"><?php echo ucfirst($_SESSION["FirstName"]); ?>'s closed tasks</strong>
						</div>
						<div class="col-xs-2">
							<a id="btnCollapseClosedTasks" data-toggle="collapse" href="#CollapseClosedTasks" aria-expanded="false" aria-controls="CollapseClosedTasks" class="btn btn-default pull-right" onclick="$(this).find('i').toggleClass('fa-minus fa-plus');"><i class="fa fa-plus"></i></a>
						</div>
					</div>
				</div>
				<div class="panel-body collapse" id="CollapseClosedTasks" style="max-height: 300px; overflow-y: scroll;">
					<div class="row">
						<div class="col-md-12">
							<?php
								include 'php/mysql_info_local.php';
								//include 'php/mysql_info.php';	
									
								$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
								
								//Error checking
								if($conn->connect_error)
								{
									die("Connection failed: " . $conn->connect_error);
								}
								$sqlcommand = "SELECT * FROM `tasks` where UserID ='" . $_SESSION["UserID"] . "' AND status = 1";
								$dataset = $conn->query($sqlcommand);
								if($dataset->num_rows > 0)	//successfully logged in / valid query
								{
									$totalClosedTasks = $dataset->num_rows;
									while($row = $dataset->fetch_assoc()) 	//get user info
									{
										$taskid = $row['TaskID'];
										$taskname = $row['TaskName'];
										$taskdescription = $row['TaskDesc'];
										$taskstatus = $row['Status'];
										echo "<div class='row'>";
										echo "<div class='col-lg-10 col-md-9 col-sm-8'>";
										
										echo "<a href='ViewTask.php?tid=$taskid' id='$taskid'>";
										echo "<b>ID: </b>$taskid";
										echo "<p>$taskname&nbsp;";
										if($taskstatus == 0)	//open
										{
											echo "<span class='label label-primary'>open</span>";
										}
										else if($taskstatus == 1)//closed
										{
											echo "<span class='label label-danger'>closed</span>";
										}
										echo "</p>";
										echo "</a>";
										echo "<strong>$taskdescription</strong>";
										echo "</div>";//./col
										echo "<div class='col-lg-2 col-md-3 col-sm-4'>";
										echo "<div class='row'>";
										echo "<div class='col-sm-12 col-xs-6'>";
										echo "<a class='btn btn-default btn-sm btn-block' href='ViewTask.php?tid=$taskid' id='$taskid'> <small>View</small></a>";
										echo "</div>";
										echo "<div class='col-sm-12 col-xs-6'>";
										echo "<a class='btn btn-success btn-sm btn-block' href='php/CloseTask.php?tid=$taskid&cmd=reopen' id='$taskid'> <small>Re-Open</small></a>";
										echo "</div>";
										echo "</div>";
										echo "</div>";//./col
										echo "</div>";//./row
										//echo "<strong>status: </strong>$taskstatus ";
										//so when you click the below link, CloseTask.php?id=`$taskid` runs
										echo "<hr>";
									}									
								}
								else 
								{
									$taskmessage = "Get To Work!";
									echo "$taskmessage";
								}
							?>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-md-12">
							<i>Total Number of Closed Tasks: </i><span class='label label-danger'><?php if(isset($totalClosedTasks)) {echo $totalClosedTasks;} else {echo "0";} ?></span>
						</div>
						<div class="col-md-12">
							<a data-toggle="modal" data-target="#ModalCreateTask" class="btn btn-primary btn-block hidden"><i class='fa fa-plus'></i> New Task</a>
						</div>
					</div>
				</div>
			</div>
			</div>
			<div id="panelReopenedTasks">
			<!--Re-Open tasks grid-->
			<div class="panel panel-success">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-10">
							<strong class="lead"><?php echo ucfirst($_SESSION["FirstName"]); ?>'s reopened tasks</strong>
						</div>
						<div class="col-xs-2">
							<a id="btnCollapseClosedTasks" data-toggle="collapse" href="#CollapseReopenedTasks" aria-expanded="false" aria-controls="CollapseReopenedTasks" class="btn btn-default pull-right" onclick="$(this).find('i').toggleClass('fa-minus fa-plus');"><i class="fa fa-plus"></i></a>
						</div>
					</div>
				</div>
				<div class="panel-body collapse" id="CollapseReopenedTasks" style="max-height: 300px; overflow-y: scroll;">
					<div class="row">
						<div class="col-md-12">
							<?php
								include 'php/mysql_info_local.php';
								//include 'php/mysql_info.php';	
									
								$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
								
								//Error checking
								if($conn->connect_error)
								{
									die("Connection failed: " . $conn->connect_error);
								}
								$sqlcommand = "SELECT * FROM `tasks` where UserID ='" . $_SESSION["UserID"] . "' AND status = 3";
								$dataset = $conn->query($sqlcommand);
								if($dataset->num_rows > 0)	//successfully logged in / valid query
								{
									$totalReopenedTasks = $dataset->num_rows;
									while($row = $dataset->fetch_assoc()) 	//get user info
									{
										$taskid = $row['TaskID'];
										$taskname = $row['TaskName'];
										$taskdescription = $row['TaskDesc'];
										$taskstatus = $row['Status'];
										echo "<div class='row'>";
										echo "<div class='col-lg-10 col-md-9 col-sm-8'>";
										
										echo "<a href='ViewTask.php?tid=$taskid' id='$taskid'>";
										echo "<b>ID: </b>$taskid";
										echo "<p>$taskname&nbsp;";
										if($taskstatus == 0)	//open
										{
											echo "<span class='label label-primary'>open</span>";
										}
										else if($taskstatus == 1)//closed
										{
											echo "<span class='label label-danger'>closed</span>";
										}
										else if($taskstatus == 3)//reopened
										{
											echo "<span class='label label-success'>reopened</span>";
										}
										echo "</p>";
										echo "</a>";
										echo "<strong>$taskdescription</strong>";
										echo "</div>";//./col
										echo "<div class='col-lg-2 col-md-3 col-sm-4'>";
										echo "<a class='btn btn-default btn-sm btn-block' href='ViewTask.php?tid=$taskid' id='$taskid'> <small>View</small></a>";
										echo "<a class='btn btn-danger btn-sm btn-block' href='php/CloseTask.php?tid=$taskid' id='$taskid'> <small>Close</small></a>";
										echo "</div>";//./col
										echo "</div>";//./row
										//echo "<strong>status: </strong>$taskstatus ";
										//so when you click the below link, CloseTask.php?id=`$taskid` runs
										echo "<hr>";
									}									
								}
								else 
								{
									$taskmessage = "Get To Work!";
									echo "$taskmessage";
								}
							?>
						</div>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-xs-12">
							<i>Total Number of Re-Opened Tasks: </i><span class='label label-success'><?php if(isset($totalReopenedTasks)) {echo $totalReopenedTasks;} else {echo "0";} ?></span>
						</div>
						<div class="col-xs-12">
							<a data-toggle="modal" data-target="#ModalCreateTask" class="btn btn-primary hidden btn-block"><i class='fa fa-plus'></i> New Task</a>
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
	<!-- /.row -->
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
<!-- ModalCreateTask -->
    <div id="ModalCreateTask" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Create New Task</h4>
                </div>
                <div class="modal-body">
					
                </div>
            </div>
        </div>
    </div>
<!-- ModalUploadProfilePic -->
    <div id="ModalUploadProfilePic" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Manage profile pic</h4>
                </div>
                <div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="error-message"><?php if(isset($UploadImgURLmessage)) { echo $UploadImgURLmessage; } ?></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<form id="UploadImgURLForm" class="form-group" method="post" action="php/UploadImgURL.php">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="imgurl">Image URL</label>
										<input id="ImageURL" type="text" class="form-control" name="imgurl" placeholder="Enter The Picture Image URL" />
										<span id="ImageURL_err"></span>
									</div>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12 clearfix">
								   <input type="submit" name="UploadImgURL" value="Upload Image URL" class="btn btn-info btn-block">
								</div>
							</div>
						</form>
					</div>
				</div>
                    
                </div>
            </div>
        </div>
    </div>	
	<!-- ModalUpdateUserInfo -->
    <div id="ModalUpdateUserInfo" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Edit User Information</h4>
                </div>
                <div class="modal-body">
				
                    
                </div>
            </div>
        </div>
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
//scripts to auto break lines  in task description
	function LineBreakertaskdesc()
    {
        this.BreakLine = function BreakLine(txt, keyCode)
        {
            var code = keyCode.keyCode;
            
            if(code == 13)  //user pressed enter
            {
                var text = txt.value;
                text = txt.value + "<br>\n";  //append br to end of text
                txt.value = text;
            }
        }
    }
    $(document).ready(function () {
        var thisFunctiontaskdesc = new LineBreakertaskdesc();
        var el = document.getElementById('taskdesc');
        el.onkeyup = function (evt) {
            thisFunctiontaskdesc.BreakLine(el, evt)
        };
    });
	</script>
    <!-- jQuery Version 1.11.1 -->
    <script language="JavaScript" type="text/javascript" src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
