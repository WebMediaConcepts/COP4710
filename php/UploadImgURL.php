<?php 
	if($_SERVER["REQUEST_METHOD"] == "POST")
	{
		// Begin new session
		session_start();
		$id = $_SESSION["UserID"];	//active user
		//store profile pic url
		$imgurl = $_POST['imgurl'];
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
	if(!empty($_POST["UploadImgURL"]))
	{
		$sqlcommand = "UPDATE `accounts` SET ImgURL = '$imgurl' WHERE UserID = $id";
		$conn->query($sqlcommand);
		$_SESSION["ImgURL"] = $imgurl;
		header("location:../index.php");	//send back to login
	}
?>