<?php

require(__DIR__ . '/../../vendor/autoload.php');
require($root . '/lib/db/user.php');
require($root . '/lib/auth.php');

require_auth();

$user_id = isset($_GET['id']) ? $_GET['id'] : $auth->getUserId();

switch ($_SERVER['REQUEST_METHOD']) {
	case 'GET':
		get_userinfo($user_id);
		break;
	case 'PUT':
		$json = file_get_contents('php://input');
		$data = json_decode($json, true);
		put_userinfo($user_id, $data);
		break;
	case 'DELETE':
		$json = file_get_contents('php://input');
		$data = json_decode($json, true);
		delete_user($user_id);
		break;
	default:
		header('Access-Control-Allow-Methods: GET, PUT, DELETE');
		http_response_code(405);
}

function get_userinfo(string $id) {
	try {
		$userinfo =  userinfo_readByUserId($id);
		if (!is_null($userinfo)) {
			return_json($userinfo->as_array());
		} else {
			return_error("User with id: " . $id . " does not exist.", 404);
		}
	} catch (PDOException $e) {
		return_error("A DB error occurred: " . $e->getMessage()
			. " Please contact an administrator.", 500);
	}
}

function put_userinfo(string $id, array $data) {
	global $auth;
	try {
		$userinfo = userinfo_readByUserId($id);
		if (!is_null($userinfo)) {
			if ($userinfo->get_id() == $auth->getUserId()) {
				if (isset($data['full_name'])) {
					$userinfo->set_full_name($data['full_name']);
				}
				if (isset($data['IBAN'])) {
					$userinfo->set_IBAN($data['IBAN']);
				}
				if (isset($data['BIC'])) {
					$userinfo->set_BIC($data['BIC']);
				}
				userinfo_update($userinfo);
				if (isset($data['email'])) {
					if (isset($data['password'])) {
						change_email($data['password'], $data['email'], $userinfo->get_full_name());
					} else {
						return_error("Password required to change email.", 403);
					}
				}
			} else if ($userinfo->get_created_by() == $auth->getUserId()) {
				if (isset($data['email'])) {
					virtualuser_updateEmail($id, $data['email']);
				} else {
					return_error("Can only modify the email address of virtual users.", 403);
				}
			} else {
				return_error("You are not authorized to modify the user info of user with id: " . $id, 403);
			}
		} else {
			return_error("User with id: " . $id . " does not exist.", 404);
		}
	} catch (PDOException $e) {
		return_error("A DB error occurred: " . $e->getMessage()
			. " Please contact an administrator.", 500);
	}
}

?>