<?php

require_once(__DIR__ . '../../vendor/autoload.php');
require_once($root . '/lib/db/conn.php');

function userinfo_create(int $user_id): UserInfo {
	global $conn;
	$query = "INSERT INTO user_info VALUES (?) RETURNING user_id;";
	$stmt = $conn->prepare($query);
	$stmt->bindParam(0, $user_id);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$results = $stmt->fetch();
	return new UserInfo($results['user_id']);
}

function userinfo_readByUserId(string $user_id): ?UserInfo {
	global $conn;
	$query = "SELECT * FROM user_info WHERE user_id = ?;";
	$stmt = $conn->prepare($query);
	$stmt->bindParam(0, $user_id);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$results=$stmt->fetch();
	if (empty($results)) {
		return null;
	}
	return new UserInfo($results['user_id'], $results['full_name'], $results['iban'], $results['bic'], $results['created_by']);
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
			$stmt->bindParam($counter, $key);
			$stmt->bindParam($counter+1, $newData->$func());
			$counter += 2;
		}
	}
	if ($counter === 0) {
		return false;
	}
	$stmt->bindParam($counter, $newData->get_id());
	return true;
}

function virtualuser_updateEmail($id, $email) {
	global $conn;
	$query = "UPDATE users SET email = ? WHERE user_id = ?;";
	$stmt = $conn->prepare($query);
	$stmt->bindParam(0, $email);
	$stmt->bindParam(1, $id);
	$stmt->execute();
}

function virtualuser_create(string $email, int $created_by) {
	global $conn;
	try {
		$conn->beginTransaction();
		$query = "INSERT INTO users (email) VALUES (?) RETURNING id;";
		$stmt = $conn->prepare($query);
		$stmt->bindParam(0, $email);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$stmt->execute();
		$id = $stmt->fetch()['id'];
		$query = "INSERT INTO user_info (user_id, created_by) VALUES (?, ?);";
		$stmt = $conn->prepare($query);
		$stmt->bindParam(0, $id);
		$stmt->bindParam(1, $created_by);
		$stmt->execute();
		$conn->commit();
	} catch (PDOException $e) {
		$conn->rollBack();
		throw $e;
	}
}

?>