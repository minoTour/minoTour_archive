<?php

parse_str(implode('&', array_slice($argv, 1)), $_GET);
$user_password_hash = password_hash($_GET['a'], PASSWORD_DEFAULT);
echo $user_password_hash;;
?>
