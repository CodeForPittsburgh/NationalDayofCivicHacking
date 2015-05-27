<!DOCTYPE html>
<html>
    <head>
        <title>Police Blotter Description Totals</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
</html>
<?php
$dbconn = pg_connect('host=cfapghpoliceblotter.cnsbqqmktili.us-east-1.rds.amazonaws.com port=5432 dbname=CfAPGHPoliceBlotter user=cfapghpolicebltrrdr password=B10tt34RDR connect_timeout=60');
/*
 * select id.descriptionid,d.section,d.description,count(id.descriptionid) incident_count
  from "PoliceBlotter2".incidentdescription id,"PoliceBlotter2".description d
  where d.descriptionid = id.descriptionid
  --and id.descriptionid in(2118,2139,2119,2121,2132,2122,2148,2166,2115,2129)
  group by d.section,d.description,id.descriptionid
  order by d.section;
 */

$sql = 'select d.section,d.description,count(id.descriptionid) incident_count
from "PoliceBlotter2".incidentdescription id,"PoliceBlotter2".description d
where d.descriptionid = id.descriptionid
--and id.descriptionid in(2118,2139,2119,2121,2132,2122,2148,2166,2115,2129)
group by d.section,d.description,id.descriptionid
order by d.section';

$result = pg_query($dbconn, $sql);
BeginIncidentTable();

while ($row = pg_fetch_row($result)) {
    //print $row[0] . ' ' . $row[1] . ' '.$row[2] . '</br>'; 
    PopulateIncidentTable($row);
}

EndIncidentTable();
WriteTest();

function BeginIncidentTable() {
    /* Display the beginning of the search results table. */
    $headings = array("Section", "Description", "Count");
    echo "<table align='center' cellpadding='5' border=1>";
    echo "<tr>";
    foreach ($headings as $heading) {
        echo "<th>$heading</th>";
    }
    echo "</tr>";
}

function PopulateIncidentTable($values) {
    /* Populate table with results. */
    echo "<tr>";
    foreach ($values as $value) {
        echo "<td>$value</td>";
    }
    echo "</tr>";
}

function EndIncidentTable() {
    echo "</table><br/>";
}

function WriteTest() {
    $dbconn = pg_connect('host=cfapghpoliceblotter.cnsbqqmktili.us-east-1.rds.amazonaws.com port=5432 dbname=CfAPGHPoliceBlotter user=cfapghpolicebltrrdr password=B10tt34RDR connect_timeout=60');
    //$dbconn = pg_connect('host=cfapghpoliceblotter.cnsbqqmktili.us-east-1.rds.amazonaws.com port=5432 dbname=CfAPGHPoliceBlotter user=CfAPGHPoliceBltr password=CfAPGH2015 connect_timeout=60');

    $sql = "insert into \"PoliceBlotter2\".description(section,description) (select '909090' as section,'Reader Test' as description where not exists (select section from \"PoliceBlotter2\".description where section='909090'))";
    if (pg_query($dbconn, $sql)) {
        print "Record added";
    } else {
        print pg_errormessage($dbconn);
        {
            
        }
    }
}
