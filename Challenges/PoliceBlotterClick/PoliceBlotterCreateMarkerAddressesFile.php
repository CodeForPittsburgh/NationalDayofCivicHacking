<?php
$timezone = "America/New_York";
date_default_timezone_set($timezone);
$seconds = 300;
set_time_limit($seconds);
$time = time();
$incidentdate = "incidentdate";
$type = "BOTH";
$my_string = filter_input(INPUT_GET, $incidentdate, FILTER_SANITIZE_STRING);
//print_r($my_string);
//echo "</br>";
if ($my_string === "") {
    //echo "incidentdate is an empty string\n";
    //echo "</br>";
}
if ($my_string === false) {
    //echo "incidentdate is false\n";
    //echo "</br>";
}
if ($my_string === null) {
    //echo "incidentdate is null\n";
    //echo "</br>";
    $yesterday = date("Y-m-d", mktime(0, 0, 0, date("n", $time), date("j", $time) - 1, date("Y", $time)));
}
if (isset($my_string)) {
    //echo "incidentdate is set\n";
    //echo "</br>";
    $yesterday = $my_string;
}
if (!empty($my_string)) {
    //echo "incidentdate is not empty\n";
    //echo "</br>";
}
$table;
$searchAddress = "2nd Ave";
//$yesterday = date("Ymd", mktime(0, 0, 0, date("n", $time), date("j", $time) - 1, date("Y", $time)));
$from = " FROM \"PoliceBlotter2\".incident";
//$where = " where incidentdate ='" . $yesterday . "'";
//$where = " where address like '%" . $searchAddress ."%'";
//$where = " where neighborhood = 'Sheraden'";
//$where = " where zone = 'Zone 6'";
$orderby = " order by address,incidentnumber";
//echo $yesterday;
//echo "\n";
$SQL = 'SELECT distinct "lat","lng","zone","address","incidentnumber"' . $from . $where . $orderby;
//echo $SQL;
//echo "</br>";

$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);
$newnode;
$myDescriptions;

$dbconn = pg_connect('host=cfapghpoliceblotter.cnsbqqmktili.us-east-1.rds.amazonaws.com port=5432 dbname=CfAPGHPoliceBlotter user=cfapghpolicebltrrdr password=B10tt34RDR connect_timeout=60');

//$result = pg_query($dbconn, 'SELECT "lat","lng","zone","incidentdate","incidentnumber","address","incidenttype" FROM "PoliceBlotter".incident where incidentdate = \'$yesterday\' ');
$result = pg_query($dbconn, $SQL);


//header("Content-type: text/xml");
// Iterate through the rows, adding XML nodes for each
//$node = $dom->createElement("marker");
//$newnode = $parnode->appendChild($node);
$rowcount = 0;
while ($row = pg_fetch_row($result)) {
    $lat[$rowcount] = $row[0];
    $lng[$rowcount] = $row[1];
    $zone[$rowcount] = rtrim($row[2]);
    $address[$rowcount] = rtrim($row[3]);
    $incidentnumber[$rowcount] = rtrim($row[4]);
    $rowcount++;
}
echo "Row Count " . $rowcount;
echo "<br>";
$dom->createElement("descriptions");
//$i;

for ($i = 0; $i < $rowcount - 1; $i++) {
    echo "(" . $i . ") " . $address[$i] . " " . $incidentnumber[$i];
    echo "<br>";
    setnode($lat[$i], $lng[$i], $zone[$i], $address[$i]);

    if ($address[$i] !== $address[$i + 1]) {
        //setnode($lat[$i], $lng[$i], $zone[$i], $address[$i]);
        $myDescriptions = "************************************************************" . "<br>";
        $table = "<html> <head></head><body><table border=\"1\"> <tr> <th> Incident Number</th> <th> Incident type</th> <th> Incident Date </th> <th> Incident Time </th> <th> Age </th> <th> Gender </th> <th> Section </th><th> Description </th></tr>";
        createdescription($incidentnumber[$i]);
        $table = $table . "</table></body></html>";
    } else {
        $table = "<html> <head></head><body><table border=\"1\"> <tr> <th> Incident Number</th> <th> Incident type</th> <th> Incident Date </th> <th> Incident Time </th> <th> Age </th> <th> Gender </th> <th> Section </th><th> Description </th></tr>";
        while ($address[$i] === $address[$i + 1]) {
            $myDescriptions = "************************************************************" . "<br>";
            //$table = "<html> <head></head><body><table border=\"1\"> <tr> <th> Incident Number</th> <th> Incident type</th> <th> Incident Date </th> <th> Incident Time </th> <th> Age </th> <th> Gender </th> <th> Section </th><th> Description </th></tr>";
            echo "(" . $i . ") " . $incidentnumber[$i] . "<br>";
            createdescription($incidentnumber[$i]);
            createdescription($incidentnumber[$i + 1]);
            $i++;
        }
        $table = $table . "</table></body></html>";
    }


    $newnode->setAttribute("description", $myDescriptions);
}
if ($i == $rowcount - 1) {
    echo "Last record <br>";
    echo $i . " " . $rowcount . "<br>";
    echo "(" . $i . ") " . $address[$i] . " " . $incidentnumber[$i];
    echo "<br>";

    setnode($lat[$i], $lng[$i], $zone[$i], $address[$i]);
    $myDescriptions = "************************************************************" . "<br>";
    $table = "<html> <head></head><body><table border=\"1\"> <tr> <th> Incident Number</th> <th> Incident type</th> <th> Incident Date </th> <th> Incident Time </th> <th> Age </th> <th> Gender </th> <th> Section </th><th> Description </th></tr>";
    createdescription($incidentnumber[$i]);
    $table = $table . "</table></body></html>";
    $newnode->setAttribute("description", $myDescriptions);
}

function setnode($lat, $lng, $zone, $address) {
// ADD TO XML DOCUMENT NODE
    global $dom;
    global $parnode;
    global $newnode;
    $node = $dom->createElement("marker");
    $newnode = $parnode->appendChild($node);


    $newnode->setAttribute("lat", $lat);
    $newnode->setAttribute("lng", $lng);
    $newnode->setAttribute("zone", $zone);
    $newnode->setAttribute("address", $address);
}

function createdescription($incidentnumber) {
    //global $dom;
    global $dbconn;
    //global $newnode;
    global $myDescriptions;
    global $table;

    //$myDescriptions = "*************** " . $incidentnumber . " ******************************** " . $myDescriptions;

    $sql2 = 'select distinct i.incidentnumber,i.incidenttype,i.incidentdate,incidenttime,i.age,i.gender,d.section,d.description from "PoliceBlotter2".description d, "PoliceBlotter2".incident i where descriptionid in (select distinct descriptionid from "PoliceBlotter2".incidentdescription where incidentnumber in (' . $incidentnumber . ')) and incidentnumber in (' . $incidentnumber . ') order by i.incidentnumber';
    echo $sql2;
    echo "<br>";

    $result2 = pg_query($dbconn, $sql2);
//$i = 0;
    /*
     * <table>
     * <tr>
     * <th>
     * </th>
     * </tr>
     * <tr>
     * <td>
     * </td>
     * </tr>
     * </table>
     */
    //$table = "<html> <head></head><body><table border=\"1\"> <tr> <th> Incident Number</th> <th> Incident type</th> <th> Incident Date </th> <th> Incident Time </th> <th> Age </th> <th> Gender </th> <th> Section </th><th> Description </th></tr>";


    while ($descriptions = pg_fetch_row($result2)) {

        //incidentnumber
        //incidenttype
        //incidentdate
        //incidenttime
        //age 
        //gender
        //section
        //description

        $myDescriptions = $myDescriptions . rtrim($descriptions[0]) . ", ";
        $myDescriptions = $myDescriptions . rtrim($descriptions[1]) . ", ";
        $myDescriptions = $myDescriptions . rtrim($descriptions[2]) . ", ";
        $myDescriptions = $myDescriptions . rtrim($descriptions[3]) . ", ";
        $myDescriptions = $myDescriptions . rtrim($descriptions[4]) . ", ";
        $myDescriptions = $myDescriptions . rtrim($descriptions[5]) . ", ";
        $myDescriptions = $myDescriptions . rtrim($descriptions[6]) . "," . rtrim($descriptions[7]) . " <br/>";

        //$node = $dom->createElement("descriptions");
        //$newnode = $parnode->appendChild($node);
        //$newnode->setAttribute("description", rtrim($descriptions[0]));
//        $node = $dom->createElement("descriptions");
//        //$newnode = $parnode->appendChild($node);
//        $newnode->setAttribute("description", $myDescriptions);
        $table = $table . "<tr>";
        $table = $table . "<td>";
        $table = $table . rtrim($descriptions[0]);
        $table = $table . "</td>";
        $table = $table . "<td>";
        $table = $table . rtrim($descriptions[1]);
        $table = $table . "</td>";
        $table = $table . "<td>";
        $table = $table . rtrim($descriptions[2]);
        $table = $table . "</td>";
        $table = $table . "<td>";
        $table = $table . rtrim($descriptions[3]);
        $table = $table . "</td>";
        $table = $table . "<td>";
        $table = $table . rtrim($descriptions[4]);
        $table = $table . "</td>";
        $table = $table . "<td>";
        $table = $table . rtrim($descriptions[5]);
        $table = $table . "</td>";
        $table = $table . "<td>";
        $table = $table . rtrim($descriptions[6]);
        $table = $table . "</td>";
        $table = $table . "<td>";
        $table = $table . rtrim($descriptions[7]);
        $table = $table . "</td>";
        $table = $table . "</tr>";
    }
    //$table = $table . "</table></body></html>";
    $myDescriptions = $table;
    echo "My Descriptions " . $myDescriptions . "<br>";
}

$filename = "xml/" . $yesterday . "marker.xml";
//echo "</br>";
//echo $filename;
//echo "</br>";
echo $dom->saveXML();
$dom->save($filename);
//$URLRedirection = '<script type="text/javascript"> document.location="PoliceBlotterOutlinePoliceMarkerXMLDataTest.html?filename=' . $filename . '";</script>';
//echo $URLRedirection;

exit();
