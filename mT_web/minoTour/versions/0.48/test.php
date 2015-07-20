<?php

// include the Diff class
require_once './class.Diff.php';

// compare two strings character by character
$diff = Diff::compare('abcmnz', 'abcmnz', true);


var_dump($diff);

 ?>
