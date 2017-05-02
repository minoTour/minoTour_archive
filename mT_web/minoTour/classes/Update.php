<?php
/**
 * Class update
 * handles user profile updates
 * Authour: Alex Payne
 */
class Update
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
     * __construct() auto starts whenever an object of this class is created i.e "$updateform = new Update();"
     */
    public function __construct()
    {
        if (isset($_POST["submitform"])) {
            $this->updateEmail();
        }
    }

    /**
     * handles the entire update process. 
     * - checks for errors
     * - connects to DB
     * - updates single row in DB
     */
    private function updateEmail()
    {
        // Sanity check on input, make sure that it fits criterea
        if (empty($_POST['user_password'])) {
            $this->errors[] = "Password is required to update account details";
        } elseif ($_POST['user_email_new'] !== $_POST['user_email_new_repeat']) {
            $this->errors[] = "Email addresses do not match";
        } elseif (strlen($_POST['user_email_new']) > 64 || strlen($_POST['user_email_new_repeat']) > 64) {
            $this->errors[] = "Email cannot be longer than 64 characters";
        } elseif (!filter_var($_POST['user_email_new'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Your email address is not in a valid email format";
        } elseif (!filter_var($_POST['user_email_new_repeat'], FILTER_VALIDATE_EMAIL)) {
            $this->errors[] = "Your email address is not in a valid email format";
        } elseif (strlen($_POST['user_email_new']) <= 64
            && filter_var($_POST['user_email_new'], FILTER_VALIDATE_EMAIL)
            && strlen($_POST['user_email_new_repeat']) <= 64
            && filter_var($_POST['user_email_new_repeat'], FILTER_VALIDATE_EMAIL)
            && ($_POST['user_email_new'] === $_POST['user_email_new_repeat'])
            && !empty($_POST['user_password'])
        ) 
        {
            // pass initial checks
            // create a database connection            
            $this->db_connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            // change character set to utf8 and check it
            if (!$this->db_connection->set_charset("utf8")) 
            {
                $this->errors[] = $this->db_connection->error;
            }

            // if no connection errors (= working database connection)
            if (!$this->db_connection->connect_errno) 
            {
                // There is no need to escape user input here as 
                // prepared statements are used to update the database
                $user_email_new = $_POST['user_email_new'];
                $user_password = $_POST['user_password'];

                // load account details with prepared statements
                $sql = "SELECT user_password_hash 
                        FROM users 
                        WHERE user_email = ?;";
                $query_load_details = $this->db_connection->prepare($sql);
                $query_load_details->bind_param("s", $_SESSION['user_email']);
                $query_load_details->execute();
                $query_load_details->store_result();
                $query_load_details->bind_result($user_password_hash);
                // check that only one result is returned and that fetch is TRUE (i.e there is a value)
                if ($query_load_details->num_rows == 1 && $query_load_details->fetch()) 
                {
                    // check the password is correct separately from previous if
                    // as this allows for useful error messages, i.e incorrect password
                    if (password_verify($user_password, $user_password_hash))
                    {
                        $sql = "UPDATE users 
                                SET user_email = ? 
                                WHERE user_email = ?";
                        $stmt = $this->db_connection->prepare($sql);
                        $stmt->bind_param("ss", $user_email_new, $_SESSION['user_email']);
                        $stmt->execute();
                        if ($stmt->errno) 
                        {
                            $this->errors[] = "An error occured, please contact the webmaster."; 
                            // appending "$stmt->error;" to this error output will tell you what the error is
                            // it is most likely that the email is alreay in use by another user
                        }
                        else 
                        {
                            $this->messages[] = "Email Address Changed.";
                            // update session vars
                            $_SESSION['user_email'] = $user_email_new;
                        }
                        $stmt->close();
                    }
                    else
                    {
                        $this->errors[] = "Password incorrect, please try again.";
                    }
                }
                else 
                {
                    // More than one row returned (shouldn't happen because email column is unique)
                    // OR password hash couldn't be retreived
                    $this->errors[] = "An error occured, please try again.";
                }

            } 
            else 
            {
                $this->errors[] = "Cannot connect to the database.";
            }
        } 
        else 
        {
            $this->errors[] = "An unknown error occurred.";
        }
    }
}