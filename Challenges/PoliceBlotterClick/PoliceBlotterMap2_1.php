<?php

//$today11m = date("m") ;
//$today11d = date("d")-1 ;
//$today11y = date("Y");
$time = time();
$yesterday = date("Ymd", mktime(0, 0, 0, date("n", $time), date("j", $time) - 1, date("Y", $time)));
$from = " FROM \"PoliceBlotter2\".incident";
$where = " where incidentdate ='" . $yesterday . "'";
$orderby = " order by incidenttype desc";
//echo $yesterday;
//echo "\n";
$SQL = 'SELECT distinct "lat","lng","zone","incidentdate","incidenttime","incidentnumber","address","incidenttype","age","gender"' . $from . $where . $orderby;
//echo $SQL;



$dbconn = pg_connect('host=cfapghpoliceblotter.cnsbqqmktili.us-east-1.rds.amazonaws.com port=5432 dbname=CfAPGHPoliceBlotter user=cfapghpolicebltrrdr password=B10tt34RDR connect_timeout=60');
//$result = pg_query($dbconn, 'SELECT "lat","lng","zone","incidentdate","incidentnumber","address","incidenttype" FROM "PoliceBlotter".incident where incidentdate = \'$yesterday\' ');
$result = pg_query($dbconn, $SQL);
$count = pg_num_rows($result);
//echo "Row count is " . $count;
if ($count > 0)
{

$dom = new DOMDocument("1.0");
$node = $dom->createElement("markers");
$parnode = $dom->appendChild($node);
header("Content-type: text/xml");

// Iterate through the rows, adding XML nodes for each
while ($row = pg_fetch_row($result)) {

    // ADD TO XML DOCUMENT NODE
    $node = $dom->createElement("marker");
    $newnode = $parnode->appendChild($node);


    $newnode->setAttribute("lat", $row[0]);
    $newnode->setAttribute("lng", $row[1]);
    $newnode->setAttribute("zone", rtrim($row[2]));
    $newnode->setAttribute("incidentdate", rtrim($row[3]));
    $newnode->setAttribute("incidenttime", rtrim($row[4]));
    $newnode->setAttribute("incidentnumber", rtrim($row[5]));
    $newnode->setAttribute("address", rtrim($row[6]));
    $type = rtrim($row[7]);
    if ($type == "OFFENSE 2.0") {
        $type = "OFFENSE";
    }
    $newnode->setAttribute("type", $type);
    $newnode->setAttribute("age", rtrim($row[8]));
    $newnode->setAttribute("gender", rtrim($row[9]));
    $myDescriptions = "*********************************************** <br/>";
    $result2 = pg_query($dbconn, 'select distinct "descriptionid" from "PoliceBlotter2".incidentdescription where "incidentnumber" = \'' . $row[5] . '\'');
    while ($incidentdescription = pg_fetch_row($result2)) {
        $result3 = pg_query($dbconn, 'select distinct "section","description" from "PoliceBlotter2".description where "descriptionid" = \'' . $incidentdescription[0] . '\'');
        while ($descriptions = pg_fetch_row($result3)) {
            $myDescriptions = $myDescriptions . rtrim($descriptions[0]) . ", " . rtrim($descriptions[1]) . "<br/>";
            //$node = $dom->createElement("descriptions");
            //$newnode = $parnode->appendChild($node);
            //$newnode->setAttribute("description", rtrim($descriptions[0]));
        }
    }
    $node = $dom->createElement("descriptions");
    //$newnode = $parnode->appendChild($node);
    $newnode->setAttribute("description", $myDescriptions);
}

$time = time();
$dow = strtolower(date("lYmd", mktime(0, 0, 0, date("n", $time), date("j", $time) - 1, date("Y", $time))));
//$yesterday2 =  date("Ymd", mktime(0,0,0,date("n", $time),date("j",$time)- 1 ,date("Y", $time)));
//echo "wednesday".$yesterday2.".xml";
//echo "</br>";
$filename = $dow . "2.xml";
//echo $filename;
//echo "</br>";;
$dom->saveXML();
echo $dom->saveXML();
//$dom->save($filename);
}

pg_close($dbconn);

?>

