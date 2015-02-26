<?php session_start(); ?>
<?php

if (strlen($_GET['username']) > 0) {
	
	$_SESSION['twittername']=$_GET['username'];

	echo "<div class='alert alert-success alert-dismissible' role='success'>  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>  Your twitter username has been set to " . $_SESSION['twittername'] . ". This setting will remain as long as you are logged in. If you close your web browser you will no longer receive twitter updates. You will also have to reset your twitter user name. We are working on a better way to do this.</div>";
} else {
echo "<div class='alert alert-success alert-dismissible' role='alert'>  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>  Something has gone wrong - your twitter username is not long enough... Please try again.</div>";
}
?>