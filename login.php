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
    }
    </style>

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">COP4710</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                   
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
<?php
	include 'php/mysql_info_local.php';
	//include 'php/mysql_info.php';	
	$conn = new mysqli($servername, $username, $password, $dbname);	//attempt connection
	
	//Error checking
	if($conn->connect_error)
	{
		die("Connection failed: " . $conn->connect_error);
	}
	if(!empty($_POST["login"]))
	{
		$sqlcommand = "SELECT * FROM accounts where email ='" . $_POST["email"] . "' AND password = '". $_POST["password"]."'";
		$dataset = $conn->query($sqlcommand);
		if($dataset->num_rows > 0)	//successfully logged in / valid query
		{
			while($row = $dataset->fetch_assoc()) 	//get user info
			{
				$userid = $row['UserID'];
				$firstname = $row['FirstName'];
				$lastname = $row['LastName'];
				$email = $row['Email'];
				$imageurl = $row['ImgURL'];
				$roleid = $row['RoleID'];
				$password = $row['Password'];
			}
			session_start();	//session values for logged in user
			$_SESSION["UserID"] = $userid;
			$_SESSION["FirstName"] = $firstname;
			$_SESSION["LastName"] = $lastname;
			$_SESSION["Email"] = $email;
			$_SESSION["ImgURL"] = $imageurl;
			$_SESSION["RoleID"] = $roleid;	//0 for regular accounts
			$_SESSION["Password"] = $password;
			header("location:index.php");
		}
		else 
		{	//invalid dataset
			$message = "Invalid Username or Password!";
		}
	}
?>
    <!-- Page Content -->
    <div class="container">

        <div class="row">
		
            <div class="col-md-4 col-md-offset-4 text-center">
			<div class="text text-danger"><?php if(isset($message)) { echo $message; } ?></div>
                <form action="" method="post" id="formLogin">
					<p>Email</p>
					<input name="email" type="text" class="form-control">
					<br>
					<p>Password</p>
					<input name="password" type="password" class="form-control">
					<br>
					<input type="submit" name="login" value="Login" class="btn btn-primary btn-block btn-lg"><br>
					<a data-toggle="modal" data-target="#ModalRegister" class="btn btn-link">Register</a>
				</form>
            </div>
        </div>
        <!-- /.row -->

    </div>
    <!-- /.container -->
<!-- ModalRegister -->
    <div id="ModalRegister" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Register for an account</h4>
                </div>
                <div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="error-message"><?php if(isset($message)) { echo $message; } ?></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<form id="RegForm" class="form-group" method="post" action="php/Register.php">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="firstname">First Name</label>
                                    <input id="fname" type="text" class="form-control" name="firstname" placeholder="Enter Your First Name" />
                                    <span id="fname_err"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="lastname">Last Name</label>
                                    <input type="text" class="form-control" name="lastname" placeholder="Enter Your Last Name" />
                                    <span id="lname_err"></span>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
							<div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="text" class="form-control" name="email" placeholder="Enter Your Email" />
                                    <span id="em_err"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" name="password" placeholder="Enter A Password" />
                                    <span id="pw_err"></span>
                                </div>
                            </div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12 clearfix">
                               <input type="submit" name="register" value="Register" class="btn btn-danger btn-block">
                            </div>
						</div>
                    </form>
					</div>
				</div>
                    
                </div>
            </div>

        </div>
    </div>
    <!-- jQuery Version 1.11.1 -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

</body>

</html>
