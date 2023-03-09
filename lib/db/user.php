<?php

require_once(__DIR__ . '../../vendor/autoload.php');
require_once($root . '/lib/db/conn.php');
require($root . '/class/UserInfo.php');

function userinfo_create(int $user_id): UserInfo {
	global $conn;
	$query = "INSERT INTO user_info VALUES (?);";
	$stmt = $conn->prepare($query);
	$stmt->bindParam(0, $user_id);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$results = $stmt->fetch();
	return new UserInfo($results['user_id']);
}

function userinfo_readByUserId(string $user_id): UserInfo {
	global $conn;
	$query = "SELECT * FROM user_info WHERE user_id = ?";
	$stmt = $conn->prepare($query);
	$stmt->bindParam(0, $user_id);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$results=$stmt->fetch();
	return new UserInfo($results['user_id'], $results['full_name'], $results['iban'], $results['bic']);
}

function userinfo_update(UserInfo $newData): bool {
	global $conn;
	$changedFields = $newData->getChangedFields();
	$query = "UPDATE user_info SET " . implode(", ", array_fill(0, count($changedFields), "? = ?")) . " WHERE user_id = ?;";
	$stmt = $conn->prepare($query);
	$counter = 0;
	foreach ($newData->getChangedFields() as $key => $value) {
		if ($value) {
			$func = "get_" . $key;
			$stmt->bindParam($counter, $newData->$func());
			$counter++;
		}
	}
	if ($counter === 0) {
		return false;
	}
	$stmt->bindParam($counter, $newData->get_id());
	return true;
}

?>