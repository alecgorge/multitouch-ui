<?php
$input = "database.csv";

unlink("alums.sqlite");
$db = new PDO('sqlite:alums.sqlite');

echo "Removing entries...<br/>\n";
$smt = $db->prepare("DELETE FROM alumni");
if(!empty($smt)) { $smt->execute(); }
else { $smt = $db->prepare("CREATE TABLE alumni (alum_id INTEGER PRIMARY KEY AUTOINCREMENT, firstname TEXT, lastname TEXT, middlename TEXT, nickname TEXT, fullname TEXT, gender TEXT, class INTEGER, hash TEXT)"); $smt->execute(); }

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
$smt = $db->prepare("INSERT into alumni (firstname,middlename,lastname,nickname,fullname,gender,class,hash) VALUES (
										 ?        ,?         ,?       ,?       ,?       ,?     ,?    ,?   )");
$db->beginTransaction();
foreach($rows as $row) {
	$smt->execute(array(
		$row[0], $row[1], $row[2], $row[3], sprintf("%s%s %s %s", $row[0], $row[1] == "NULL" ? "" : " ".$row[1], $row[3] == "NULL" ? $row[0] : $row[3], $row[2]),$row[4], $row[5], md5($row[0].$row[1].$row[2].$row[5])
	));
}
$db->commit();
echo "Done with SQL!<br/>\n";

system("export.bat");

echo "<br/>\nExported to dump.sql<br/>\n";
