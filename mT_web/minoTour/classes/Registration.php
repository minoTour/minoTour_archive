<?php

/**
 * Class registration
 * handles the user registration
 */
class Registration
{
    /**
     * @var object $db_connection The database connection
     */
    private $db_connection = null;
    /**
     * @var array $errors Collection of error messages
     */
    public $errors = array();
    /**
     * @var array $messages Collection of success / neutral messages
     */
    public $messages = array();

    /**
     * the function "__construct()" automatically starts whenever an object of this class is created,
     * you know, when you do "$registration = new Registration();"
     */
    public function __construct()
    {
        if (isset($_POST["register"])) {
            $this->registerNewUser();
        }
    }

    /**
     * handles the entire registration process. checks all error possibilities
     * and creates a new user in the database if everything is fine
     */
    private function registerNewUser()
    {
        if (empty($_POST['user_name'])) {
            $this->errors[] = "<div class='row'>
                    <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    Empty Username
                    </div></div></div>";
        } elseif (empty($_POST['user_password_new']) || empty($_POST['user_password_repeat'])) {
            $this->errors[] = "<div class='row'>
                    <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    Empty Password.
                    </div></div></div>";
        } elseif ($_POST['user_password_new'] !== $_POST['user_password_repeat']) {
            $this->errors[] = "<div class='row'>
                    <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    Passwords don't match - please try again.
                    </div></div></div>";
        } elseif (strlen($_POST['user_password_new']) < 6) {
            $this->errors[] = "<div class='row'>
                    <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    Your password has to ne at least 6 characters long.
                    </div></div></div>";
        } elseif (strlen($_POST['user_name']) > 64 || strlen($_POST['user_name']) < 2) {
            $this->errors[] = "<div class='row'>
                    <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    Username cannot be shorter than 2 or longer than 64 characters.
                    </div></div></div>";
        } elseif (!preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])) {
            $this->errors[] = "<div class='row'>
                    <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    Username does not fit the name scheme: only a-Z and numbers are allowed, 2 to 64 characters.
                    </div></div></div>";
        } elseif (empty($_POST['user_email'])) {
            $this->errors[] = "<div class='row'>
                    <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    Email cannot be empty.
                    </div></div></div>";
        } elseif (strlen($_POST['user_email']) > 64) {
            $this->errors[] = "<div class='row'>
                    <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    Email cannot be longer than 64 characters.
                    </div></div></div>";
        } elseif (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "<div class='row'>
                    <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
          <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
                    Your email address is not in a valid email format.
                    </div></div></div>";
        } elseif (!empty($_POST['user_name'])
            && strlen($_POST['user_name']) <= 64
            && strlen($_POST['user_name']) >= 2
            && preg_match('/^[a-z\d]{2,64}$/i', $_POST['user_name'])
            && !empty($_POST['user_email'])
            && strlen($_POST['user_email']) <= 64
            && filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)
            && !empty($_POST['user_password_new'])
            && !empty($_POST['user_password_repeat'])
            && ($_POST['user_password_new'] === $_POST['user_password_repeat'])
        ) {
            // create a database connection
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // change character set to utf8 and check it
            if (!$this->db_connection->set_charset("utf8")) {
                $this->errors[] = $this->db_connection->error;
            }

            // if no connection errors (= working database connection)
            if (!$this->db_connection->connect_errno) {

                // escaping, additionally removing everything that could be (html/javascript-) code
                $user_name = $this->db_connection->real_escape_string(strip_tags($_POST['user_name'], ENT_QUOTES));
                $user_email = $this->db_connection->real_escape_string(strip_tags($_POST['user_email'], ENT_QUOTES));

                $user_password = $_POST['user_password_new'];

                // crypt the user's password with PHP 5.5's password_hash() function, results in a 60 character
                // hash string. the PASSWORD_DEFAULT constant is defined by the PHP 5.5, or if you are using
                // PHP 5.3/5.4, by the password hashing compatibility library
                $user_password_hash = password_hash($user_password, PASSWORD_DEFAULT);

                // check if user or email address already exists
                $sql = "SELECT * FROM users WHERE user_name = '" . $user_name . "' OR user_email = '" . $user_email . "';";
                $query_check_user_name = $this->db_connection->query($sql);

                if ($query_check_user_name->num_rows == 1) {
                    $this->errors[] = "<div class='row'>
				            <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
				  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
							Sorry, that username / email address is already taken.
							</div></div></div>";
                } else {
                    // write new user's data into database
                    $sql = "INSERT INTO users (user_name, user_password_hash, user_email)
                            VALUES('" . $user_name . "', '" . $user_password_hash . "', '" . $user_email . "');";
                    $query_new_user_insert = $this->db_connection->query($sql);

                    // if user has been added successfully
                    if ($query_new_user_insert) {
                        $this->messages[] = " <div class='row'>
            <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
  Your account has been created successfully. You can now <a href='index.php'>log in</a>.
</div></div></div>";
                    } else {
                        $this->errors[] = "<div class='row'>
				            <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
				  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
							Sorry, your registration failed. Please go back and try again.
							</div></div></div>";
                    }
                }
            } else {
                $this->errors[] = "<div class='row'>
				            <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
				  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
				  Sorry, no database connection.
				</div></div></div>";
            }
        } else {
            $this->errors[] = "<div class='row'>
				            <div class='col-md-4 col-md-offset-4'><div class='alert alert-warning alert-dismissible' role='alert'>
				  <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
				  An unknown error occurred.
				</div></div></div>
				";
        }
    }
}
