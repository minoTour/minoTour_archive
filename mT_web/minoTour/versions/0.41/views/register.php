<?php
// show potential errors / feedback (from registration object)
if (isset($registration)) {
    if ($registration->errors) {
        foreach ($registration->errors as $error) {
            echo $error;
        }
    }
    if ($registration->messages) {
        foreach ($registration->messages as $message) {
            echo $message;
        }
    }
}
?>
<!DOCTYPE html>
<html>

<?php include "includes/head.php";?>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="login-panel panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Please Register For An Account</h3>
                    </div>
                    <div class="panel-body">
						<!-- register form -->
						<form method="post" action="register.php" name="registerform">
                            <fieldset>
                                <div class="form-group">
    <!-- the user name input field uses a HTML5 pattern check -->
    <label for="login_input_username">Username (only letters and numbers, 2 to 64 characters)</label>
    <input id="login_input_username" class="form-control" type="text" pattern="[a-zA-Z0-9]{2,64}" name="user_name" required />
</div>
                            <fieldset>
                                <div class="form-group">
    <!-- the email input field uses a HTML5 email type check -->
    <label for="login_input_email">User's email</label>
    <input id="login_input_email" class="form-control" type="email" name="user_email" required />
</div>
    <label for="login_input_password_new">Password (min. 6 characters)</label>
    <input id="login_input_password_new" class="form-control" type="password" name="user_password_new" pattern=".{6,}" required autocomplete="off" />
                            <fieldset>
                                <div class="form-group">
    <label for="login_input_password_repeat">Repeat password</label>
    <input id="login_input_password_repeat" class="form-control" type="password" name="user_password_repeat" pattern=".{6,}" required autocomplete="off" />
</div>
    <input type="submit"  class="btn btn-lg btn-success btn-block" name="register" value="Register" />

</form>

                                
<!-- backlink -->
<a href="index.php">Back to Login Page</a>

                    </div>
                </div>
				
            </div>
			
        </div>
		
    </div>

    <!-- Core Scripts - Include with every page -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>

    <!-- SB Admin Scripts - Include with every page -->
    <script src="js/sb-admin.js"></script>

<?php include "includes/reporting.php";?>
</body>

</html>
