<?php

function middle_initial ($x) {
	return $x == "NULL" ? "" : substr($x,0,1).".";
}

$query = $_GET['query'];
$args = json_decode($_GET['args'] ? $_GET['args'] : "[]");

if(empty($query)) {
	echo json_encode(array(
		"result" => "error",
		"error" => "No query passed",
		"rows" => array()
	));
	exit();
}
try {
	$db = new PDO('sqlite:db/alums.sqlite');
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	file_put_contents("SQL.log", $query."\r\n".$_GET['args']."\r\n\r\n", FILE_APPEND);
	$smt = $db->prepare($query);
	
	$smt->execute((array)$args);
	
	// $res = 0;
	// if(array_search(strtoupper(substr($query, 0, 6)), array(
		// "INSERT",
		// "DELETE",
		// "UPDATE"
	// ))) {
		// $res = $smt->
	// }
	
	echo json_encode(array(
		"result" => "success",
		"rows" => $smt->fetchAll(PDO::FETCH_ASSOC)
	));
	exit();	
}
catch (Exception $e) {
	echo json_encode(array(
		"result" => "error",
		"error" => $e->getMessage(),
		"rows" => array()
	));
	exit();
}

