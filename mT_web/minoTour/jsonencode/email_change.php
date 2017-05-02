<?php
require_once("../config/db.php");
require_once("../classes/Login.php");
$login = new Login();
if ($login->isUserLoggedIn() == true) 
{
    require_once("../classes/Update.php");
    $updateform = new Update();
    $messagewrap_success = "<div class='alert alert-success alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><strong>Success!</strong>";
    $messagewrap_failure = "<div class='alert alert-danger alert-dismissible' role='alert'><button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button><strong>Error!</strong>";
    if (isset($updateform)) 
    {
        if ($updateform->errors) 
        {
            foreach ($updateform->errors as $error) 
            {
                echo $messagewrap_failure." ".$error."</div>";
            }
        }
        if ($updateform->messages) 
        {
            foreach ($updateform->messages as $message) 
            {
                echo $messagewrap_success." ".$message."</div>";
            }
        }
    }
}
else
{
    echo $messagewrap_failure." Account is currently not signed in.</div>";
}
?>