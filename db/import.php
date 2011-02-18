<?php
echo "Reading input.csv<br/>\n";
$rows = array();
if (($handle = fopen("input.csv", "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
		$rows[] = $data;
    }
    fclose($handle);
}
array_shift($rows);

$db = new PDO('sqlite:alums.sqlite');
$smt = $db->prepare("INSERT into alumni (firstname,middlename,lastname,nickname,gender,class,hash) VALUES (
										 ?        ,?         ,?       ,?        ,?     ,?    ,?   )");
//var_dump($smt,$db->errorInfo());
$db->beginTransaction();
foreach($rows as $row) {
	$smt->execute(array(
		$row[0], $row[1], $row[2], $row[3], $row[4], $row[5], md5($row[0].$row[1].$row[2])
	));
}
$db->commit();
echo "Done!";
