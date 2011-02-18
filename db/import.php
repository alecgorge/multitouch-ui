<?php
$input = "db_5indee.csv";
$db = new PDO('sqlite:alums.sqlite');

echo "Removing entries...<br/>\n";
$smt = $db->prepare("DELETE FROM alumni");
$smt->execute();

echo "Reading $input...<br/>\n";
$rows = array();
if (($handle = fopen($input, "r")) !== false) {
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
		$rows[] = $data;
    }
    fclose($handle);
}
array_shift($rows);

echo "Inserting into DB<br/>\n";
$smt = $db->prepare("INSERT into alumni (firstname,middlename,lastname,nickname,gender,class,hash) VALUES (
										 ?        ,?         ,?       ,?        ,?     ,?    ,?   )");
//var_dump($smt,$db->errorInfo());
$db->beginTransaction();
foreach($rows as $row) {
	$smt->execute(array(
		$row[0], $row[1], $row[2], $row[3], $row[4], $row[5], md5($row[0].$row[1].$row[2].$row[5])
	));
}
$db->commit();
echo "Done!";
