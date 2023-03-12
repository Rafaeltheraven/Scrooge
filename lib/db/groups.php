<?php

require_once(__DIR__ . '../../vendor/autoload.php');
require_once($root . '/lib/db/conn.php');
require($root . '/class/UserInfo.php');

function group_create(int $user_id): int {
	global $conn;
	$query = "INSERT INTO groups (owner) VALUES (?);";
	$stmt = $conn->prepare($query);
	$stmt->bindParam(0, $user_id);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$results = $stmt->fetch();
	return new $results['id'];
}

function group_insert_members(int $user_id): int {
	global $conn;
	$query = "INSERT INTO group_members (member_id) VALUES (?);";
	$stmt = $conn->prepare($query);
	$stmt->bindParam(0, $user_id);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$results = $stmt->fetch();
	return new UserInfo($results['user_id']);
}

function group_readPaymentsByGroupId(int $id): int {
	global $conn;
	$query = "SELECT member_id as Creditor, GP.id, amount, description, date_time, debtor_id, payback from group_payments as GP 
	join group_payments_debtor as GPD on GP.id = GPD.payment_id 
	join group_members as GM on GP.creditor = GM.member_id
	where GP.id = ?";
	$stmt = $conn->prepare($query);
	$stmt->bindParam(0, $id);
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$results = $stmt->fetch();
	return new results($results['GP.id']);
}
