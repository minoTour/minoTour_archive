<?php

session_start();
require_once("../config/db.php");

$mindb_connection = new mysqli(DB_HOST,DB_USER,DB_PASS,$_SESSION['active_run_name']);

$queryarray;
$querycheck = "SELECT * from alerts;";
$querycheckresult = $mindb_connection->query($querycheck);
foreach ($querycheckresult as $row) {
    //echo $row['name'] . "<br>";
    $queryarray[$row['name']][$row['alert_index']]['reference']=$row['reference'];
    $queryarray[$row['name']][$row['alert_index']]['threshold']=$row['threshold'];
    $queryarray[$row['name']][$row['alert_index']]['control']=$row['control'];
    $queryarray[$row['name']][$row['alert_index']]['complete']=$row['complete'];
    $queryarray[$row['name']][$row['alert_index']]['start']=$row['start'];
    $queryarray[$row['name']][$row['alert_index']]['end']=$row['end'];
    $queryarray[$row['name']][$row['alert_index']]['alert_index']=$row['alert_index'];

}
if ($_SESSION['currentbarcode'] >= 1) {
    if (isset($queryarray["barcodecoverage"])){
        echo "<Strong>Individual Barcode Thesholds Set.</strong><br><br>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-condensed'>";
        echo "<tr>";
        echo "<th>Reference</th>";
        echo "<th>Threshold</th>";
        echo "<th>Control</th>";
        echo "<th>Complete</th>";
        //echo "<th>Remove</th>";
        echo "</tr>";
        foreach ($queryarray["barcodecoverage"] as $entry) {
            echo "<tr>";
            echo "<td style='word-wrap: break-word'>";
            echo $entry['reference'];
            echo "</td>";
            echo "<td>";
            echo $entry['threshold'];
            echo "</td>";
            echo "<td>";
            echo $entry['control'];
            echo "</td>";
            echo "<td>";
            echo $entry['complete'];
            echo "</td>";
            //echo "<td>";
            //echo "Remove";
            //echo "</td>";

        }
        echo "</table>";
        echo "</div>";
        echo "<button id='removethresholds' type='button' class='btn btn-danger btn-xs'>Remove Thresholds</button><br><br>";

    }else{
        echo "<em>No Individual Barcode Thresholds Set.</em><br><br>";
    }
    if (isset($queryarray["genbarcodecoverage"])){
        echo "<Strong>Global Barcode Theshold Set.</strong><br><br>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-condensed'>";
        echo "<tr>";
        echo "<th>Reference</th>";
        echo "<th>Threshold</th>";
        echo "<th>Control</th>";
        echo "<th>Complete</th>";
        echo "</tr>";
        foreach ($queryarray["genbarcodecoverage"] as $entry) {
            echo "<tr>";
            echo "<td>";
            //echo $entry['reference'];
            echo "All Barcodes";
            echo "</td>";
            echo "<td>";
            echo $entry['threshold'];
            echo "</td>";
            echo "<td>";
            echo $entry['control'];
            echo "</td>";
            echo "<td>";
            echo $entry['complete'];
            echo "</td>";
        }
        echo "</table>";
        echo "</div>";
        echo "<button id='removeglobthreshold' type='button' class='btn btn-danger btn-xs'>Remove Global Threshold</button><br><br>";
    }else{
        echo "<em>No Global Barcode Theshold Set.</em><br><br>";
    }
}
if (isset($queryarray["referencecoverage"])){
    echo "<Strong>Reference Coverage Thesholds Set.</strong><br><br>";
    echo "<div class='table-responsive'>";
    echo "<table class='table table-responsive'>";
    echo "<tr>";
    echo "<th>Ref</th>";
    echo "<th>Limit</th>";
    echo "<th>Start</th>";
    echo "<th>End</th>";
    echo "<th>Cont.</th>";
    echo "<th>Done</th>";
    echo "<th>Del</th>";
    echo "</tr>";
    foreach ($queryarray["referencecoverage"] as $entry) {
        echo "<tr>";
        echo "<td>";
        echo $entry['reference'];
        echo "</td>";
        echo "<td>";
        echo $entry['threshold'];
        echo "</td>";
        echo "<td>";
        echo $entry['start'];
        echo "</td>";
        echo "<td>";
        echo $entry['end'];
        echo "</td>";
        echo "<td>";
        echo $entry['control'];
        echo "</td>";
        echo "<td>";
        echo $entry['complete'];
        echo "</td>";
        echo "<td>";
        echo "<button id='removeref";
        echo $entry['alert_index'];
        echo "' type='button' value='" . $entry['alert_index'] . "' class='btn btn-danger btn-xs'>Remove</button><br><br>";
        echo "</td>";

    }
    echo "</table>";
    echo "</div>";
}else{
    echo "<em>No Reference Coverage Thesholds Set.</em><br><br>";
}
echo json_encode($queryarray);
echo "<br><br>";
echo json_encode($queryarray[referencecoverage]);
echo "<br><br>";
echo json_encode($queryarray[gencoverage]);
echo "<br><br>";
echo json_encode($queryarray[barcodecoverage]);
echo "<br><br>";
echo json_encode($queryarray[genbarcodecoverage]);
echo "<br><br>";
echo json_encode($queryarray[basenotification]);


?>
