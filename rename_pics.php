<?php

$dir = "alumni_pics/*/*.jpg";

$skip = array();

$db = new PDO('sqlite:./db/alumni.sqlite');

$exact_match = $db->prepare("SELECT * FROM alumni WHERE (class = ? AND lastname = ?) AND (firstname = ? OR middlename = ? OR nickname = ?)");
$all = $db->prepare("SELECT * FROM alumni WHERE class = ?");

$filesToRevisit = array();
$renames = array();

foreach(glob($dir) as $file) {
	$filename = basename($file);

	if(array_search(end(explode('/', trim(str_replace('\\', '/', dirname($file)), '/'))), $skip) === false) { // not in the skip
		$params = explode('_', reset(explode('.', $filename)));
		$params[0] = (int)$params[0];
		$params[3] = $params[2];
		$params[4] = $params[2];

		$new = array();

		$exact_match->execute($params);
		$rows = $exact_match->fetchAll(PDO::FETCH_ASSOC);
		$newRawRow = array();
		if(count($rows) > 0) {
			$newRawRow = $rows[0];
		}
		else {
			$filesToRevisit[] = $file;
			continue;
		}

		$new = array(
			$newRawRow['class'],
			$newRawRow['lastname'],
			$newRawRow['middlename'],
			$newRawRow['nickname'],
			$newRawRow['firstname'],
		);
		
		$newFilename = implode('_', $new).'.jpg';

		$renames[$file] = dirname($file).'/'.$newFilename;
	}
}

$numRecovered = 0;
foreach($filesToRevisit as $k => $file) {
	$filename = basename($file);

	$params = explode('_', reset(explode('.', $filename)));
	$params[0] = (int)$params[0];
	$params[3] = $params[2];
	$params[4] = $params[2];

	$all->execute(array($params[0]));
	$rows = $all->fetchAll(PDO::FETCH_ASSOC);

	$dists = array();
	foreach($rows as $k => $row) {
		$dists[] = levenshtein($params[1], $row['lastname']) +
				       min(levenshtein($params[2], $row['firstname']),
				   	       levenshtein($params[2], $row['middlename']),
				           levenshtein($params[2], $row['nickname']));
	}

	$newRawRow = $rows[reset(array_keys($dists, min($dists)))];

	$new = array(
		$newRawRow['class'],
		$newRawRow['lastname'],
		$newRawRow['middlename'],
		$newRawRow['nickname'],
		$newRawRow['firstname'],
	);
	
	$newFilename = implode('_', $new).'.jpg';
	$newPath = dirname($file).'/'.$newFilename;

	if(array_search($newPath, $renames) !== false) {
		printf("It would seem that %s is already taken. Does this person even exist: %s\n", $newFilename, $filename);
	}
	else {
		$numRecovered++;
		$renames[$file] = $newPath;
	}
}
printf("%d pictures are going to be fixed.\n", $numRecovered);

foreach($renames as $from => $to) {
	printf("Renaming: %s\n      to: %s\n", basename($from), basename($to));
}
