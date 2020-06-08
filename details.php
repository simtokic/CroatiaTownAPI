<?php
 
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
 
include_once 'config/database.php';
include_once 'objects/county.php';
include_once 'objects/town.php';
include_once 'objects/community.php';
 
if (isset($_GET['entityType']))
    $entityType = $_GET['entityType'];
else
    $entityType = "";

if (isset($_GET['ID']))
    $ID = $_GET['ID'];
else
    $ID = "";

if (isset($_GET['v']))
    $version = $_GET['v'];
else
    $version = "1";

if ($entityType != "1" && $entityType != "2" && $entityType != "3") {
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

if (!is_numeric($ID)) {
	http_response_code(400);
	echo json_encode(
        array("error" => "BadRequest")
    );
	die();
}

$database = new Database();
$db = $database->getConnection();

if($entityType == "1")
	$entity = new County($db);
else if($entityType == "2")	
	$entity = new Town($db);
else if($entityType == "3")	
	$entity = new Community($db);

$entity->id = $ID;
$entity->readOne();

if($entity->name === NULL) {
	http_response_code(404);
	echo json_encode(
        array("error" => "NoRecordsFound")
    );
	die();
}

if($entityType != "1")
	$record = array(
		"entityType" => intval($entityType),
		"ID" => intval($entity->id),
		"name" => $entity->name,
		"countyID" => intval($entity->county_id),
		"countyName" => $entity->county_name,
		"address" => $entity->address,
		"zipCode" => intval($entity->zipCode),
		"phone" => $entity->phone,
		"fax" => $entity->fax,
		"email" => $entity->email,
		"web" => $entity->web,
		"governor" => $entity->governor,
		"viceGovernor" => $entity->viceGovernor,
		"viceGovernor2" => $entity->viceGovernor2,
		"viceNationalMinority" => $entity->viceMinority,
		"representativeBodyPresident" => $entity->representativeBodyPresident
	);
else
	$record = array(
		"entityType" => intval($entityType),
		"ID" => intval($entity->id),
		"name" => $entity->name,
		"address" => $entity->address,
		"zipCode" => intval($entity->zipCode),
		"phone" => $entity->phone,
		"fax" => $entity->fax,
		"email" => $entity->email,
		"web" => $entity->web,
		"governor" => $entity->governor,
		"viceGovernor" => $entity->viceGovernor,
		"viceGovernor2" => $entity->viceGovernor2,
		"viceNationalMinority" => $entity->viceMinority,
		"representativeBodyPresident" => $entity->representativeBodyPresident
	);
	
print_r(json_encode($record));

?>