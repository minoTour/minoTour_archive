<?php

header('Access-Control-Allow-Origin: *'); 
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
require_once("../config/db.php");
$twitteruser = urldecode($_GET["twitteruser"]);
$run = urldecode($_GET["run"]);
$user = urldecode($_GET["user"]);
$message = urldecode($_GET["message"]);

$today = date("F j, Y, g:i a");

#$mymessage = "@" . $twitteruser . " " . $today . " " . $run . " " . $user . " " . $message;
$mymessage = $message . " " . $today;
//echo "whats going down<BR>";

//echo $mymessage;

// require codebird
require_once('remotetweet/codebird.php');
 
\Codebird\Codebird::setConsumerKey(consumerkey, consumersecret);
$cb = \Codebird\Codebird::getInstance();
$cb->setToken(accesstoken, accesssecret);

//echo consumerkey;
 
$params = array(
  'status' => $mymessage
);
$reply = $cb->statuses_update($params);

//echo "Page Loaded<br>";

?>
