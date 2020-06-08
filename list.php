<?php

/**
 * @api {get} /list.php?v=1&entityType=:entityType Read list
 * @apiVersion 1.0.0
 * @apiName ReadList
 * @apiGroup CroatiaTownAPI
 *
 * @apiParam {Number=1,2,3} [entityType] Type of entity (counties, towns or communities).
 * @apiParam {Number=1} [v=1] API version.
 *
 * @apiSuccess {Object[]} counties  List of counties.
 * @apiSuccess {Number} counties.entityType  Entity type (1).
 * @apiSuccess {Number} counties.ID  County ID.
 * @apiSuccess {String} counties.name  County name.
 * @apiSuccess {Object[]} towns  List of towns.
 * @apiSuccess {Number} towns.entityType  Entity type (2).
 * @apiSuccess {Number} towns.ID  Town ID.
 * @apiSuccess {String} towns.name  Town name.
 * @apiSuccess {Number} towns.countyID  Town county ID.
 * @apiSuccess {String} towns.countyName  Town county name.
 * @apiSuccess {Object[]} communities  List of communities. 
 * @apiSuccess {Number} communities.entityType  Entity type (3).
 * @apiSuccess {Number} communities.ID  Community ID.
 * @apiSuccess {String} communities.name  Community name.
 * @apiSuccess {Number} communities.countyID  Community county ID.
 * @apiSuccess {String} communities.countyName  Community county name.
 *
 * @apiError NoRecordsFound No records found. 
 * @apiError BadRequest Bad request.
 * @apiError WrongAPIVersion Wrong API version.
 *
 * @apiSampleRequest https://tehcon.com.hr/api/CroatiaTownAPI/list.php
 *
 */
 
 /**
 * @api {get} /details.php?v=1&entityType=:entityType&ID=:ID Read details
 * @apiVersion 1.0.0
 * @apiName ReadDetails
 * @apiGroup CroatiaTownAPI
 *
 * @apiParam {Number=1,2,3} entityType Type of entity (county, town or community).
 * @apiParam {Number} ID ID of entity.
 * @apiParam {Number=1} [v=1] API version.
 *
 * @apiSuccess {Number} entityType  Entity type.
 * @apiSuccess {Number} ID  ID of county, town or community.
 * @apiSuccess {String} name  Name of entity.
 * @apiSuccess {Number} [countyID]  Town or community county ID.
 * @apiSuccess {String} [countyName]  Town or community county name.
 * @apiSuccess {String} address  Main address of entity.
 * @apiSuccess {Number} zipCode  Zip code of entity.
 * @apiSuccess {String} phone  One or more Phones of entity (comma delimited).
 * @apiSuccess {String} fax  One or more Faxes of entity (comma delimited).
 * @apiSuccess {String} email  One or more Emails of entity (comma delimited).
 * @apiSuccess {String} web  Web site of entity.
 * @apiSuccess {String} governor  Name and surname of governor of entity.
 * @apiSuccess {String} viceGovernor  Name and surname of vice governor of entity.
 * @apiSuccess {String} viceGovernor2  Name and surname of second vice governor of entity.
 * @apiSuccess {String} viceNationalMinority  Name, surname and description of vice governor from national minorities.
 * @apiSuccess {String} representativeBodyPresident  Name and surname of representative body president.
 *
 * @apiError NoRecordsFound No records found. 
 * @apiError BadRequest Bad request.
 * @apiError WrongAPIVersion Wrong API version.
 *
 * @apiSampleRequest https://tehcon.com.hr/api/CroatiaTownAPI/details.php
 *
 */

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once 'config/database.php';
include_once 'objects/county.php';
include_once 'objects/town.php';
include_once 'objects/community.php';
 
if (isset($_GET['entityType']))
    $type = $_GET['entityType'];
else
    $type = "";

if (isset($_GET['v']))
    $version = $_GET['v'];
else
    $version = "1";

if ($type != "" && $type != "1" && $type != "2" && $type != "3") {
	http_response_code(400);
	echo json_encode(
        array("error" => "BadRequest")
    );
	die();
}

if ($version != "1") {
	http_response_code(400);
	echo json_encode(
        array("error" => "WrongAPIVersion")
    );
	die();
}

$database = new Database();
$db = $database->getConnection();

if($type == "" || $type == "1") {
	$county = new County($db);
	$stmt_county = $county->read();
	$num_county = $stmt_county->rowCount();
}

if($type == "" || $type == "2") {
	$town = new Town($db);
	$stmt_town = $town->read();
	$num_town = $stmt_town->rowCount();
}

if($type == "" || $type == "3") {
	$community = new Community($db);
	$stmt_community = $community->read();
	$num_community = $stmt_community->rowCount();
}

$records=array();

if(($type == "" || $type == "1") && $num_county > 0){
 
    $records["counties"]=array();
 
    while ($row = $stmt_county->fetch(PDO::FETCH_ASSOC)){
		
        extract($row);
 
        $county_item=array(
			"entityType" => 1,
            "ID" => $id,
            "name" => $name
        );
 
        array_push($records["counties"], $county_item);
    }
	
}
 
if(($type == "" || $type == "2") && $num_town > 0){
 
    $records["towns"]=array();
 
    while ($row = $stmt_town->fetch(PDO::FETCH_ASSOC)){
		
        extract($row);
 
        $town_item=array(
			"entityType" => 2,
            "ID" => $id,
            "name" => $name,
            "countyID" => $county_id,
            "countyName" => $county_name
        );
 
        array_push($records["towns"], $town_item);
    }
	
}

if(($type == "" || $type == "3") && $num_community > 0){
 
    $records["communities"]=array();
 
    while ($row = $stmt_community->fetch(PDO::FETCH_ASSOC)){

        extract($row);
 
        $community_item=array(
			"entityType" => 3,
            "ID" => $id,
            "name" => $name,
            "countyID" => $county_id,
            "countyName" => $county_name
        );
 
        array_push($records["communities"], $community_item);
    }
 
}
 
if($num_county < 1 && $num_town < 1 && $num_community < 1){
    http_response_code(404);
	echo json_encode(
        array("error" => "NoRecordsFound")
    );
}
else
	echo json_encode($records, JSON_NUMERIC_CHECK);
?>