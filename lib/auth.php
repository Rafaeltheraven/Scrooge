<?php

require(__DIR__ . '/../vendor/autoload.php');
require($root . '/lib/db/conn.php');
require($root . '/lib/db/user.php');
require($root . '/lib/utils.php');

$auth = new Delight\Auth\Auth($conn);

function register_user(string $email, string $password, string $username): int {
	global $auth;
	if (preg_match('/[\x00-\x1f\x7f\/:\\\\]/', $username, $matches) === 0) {
		try {
			$userID = $auth->registerWithUniqueUsername($email, $password, $username, function ($selector, $token) use ($email, $username) {
				send_verification_email($selector, $token, $email, $username);
			});
			return $userID;
		} catch (Exception $e) {
			handle_exception_default($e);
			return -1;
		}
	} else {
		return_error("Your username contained the following invalid characters: " . implode(", ", array_slice($matches, 1)), 422);
		return -1;
	}
}

function login(string $username, string $password): bool {
	global $auth;
	try {
		$auth->loginWithUsername($username, $password, get_remember_duration());
		userinfo_create($auth->getUserId());
		return true;
	} catch (Delight\Auth\InvalidPasswordException | Delight\Auth\UnknownUsernameException | Delight\Auth\AmbiguousUsernameException $e) {
		return_error('Invalid username or password or email has not been verified.', 403);
		return false;
	} catch (Exception $e) {
		handle_exception_default($e);
		return false;
	}
}

function verify_email(string $selector, string $token): bool {
	global $auth;
	try {
		$auth->confirmEmailAndSignIn($selector, $token, get_remember_duration());
		return true;
	} catch (Exception $e) {
		handle_exception_default($e);
		return false;
	}
}

function reset_password_request(string $email, string $username): bool {
	global $auth;
	try {
		$auth->forgotPassword($email, function ($selector, $token) use ($email, $username) {
			send_reset_email($selector, $token, $email, $username);
		});
		return true;
	} catch (Delight\Auth\InvalidEmailException | Delight\Auth\EmailNotVerifiedException $e) {
		return_error("Email does not exist or has not been verified.", 400);
		return false;
	} catch (Exception $e) {
		handle_exception_default($e);
		return false;
	}
}

function reset_password_validate(string $selector, string $token): bool {
	global $auth;
	try {
		$auth->canResetPasswordOrThrow($selector, $token);
		return true;
	} catch (Exception $e) {
		handle_exception_default($e);
		return false;
	}
}

function reset_password_finalize(string $selector, string $token, string $password): bool {
	global $auth;
	try {
		$auth->resetPasswordAndSignIn($selector, $token, $password, get_remember_duration());
		return true;
	} catch (Exception $e) {
		handle_exception_default($e);
		return false;
	}
}

function change_password(string $old, string $new): bool {
	global $auth;
	try {
		$auth->changePassword($old, $new);
		return true;
	} catch (Exception $e) {
		handle_exception_default($e);
		return false;
	}
}

function change_email(string $password, string $email, string $username): bool {
	global $auth;
	try {
		if ($auth->reconfirmPassword($password)) {
			$auth->changeEmail($email, function ($selector, $token) use ($email, $username) {
				send_verification_email($selector, $token, $email, $username);
			});
			return true;
		} else {
			return_error("Wrong password.", 403);
			return false;
		}
	} catch (Exception $e) {
		handle_exception_default($e);
		return false;
	}
}

function resend_confirmation_email(string $email, string $username): bool {
	global $auth;
	try {
		$auth->resendConfirmationForEmail($email, function ($selector, $token) use ($email, $username) {
			send_verification_email($selector, $token, $email, $username);
		});
		return true;
	} catch (Delight\Auth\ConfirmationRequestNotFound $e) {
		return_error("Have not sent verification email before.", 404);
		return false;
	} catch (Exception $e) {
		handle_exception_default($e);
		return false;
	}
}

function logout(): bool {
	global $auth;
	try {
		$auth->logOut();
		$auth->destroySession();
		return true;
	} catch (Exception $e) {
		handle_exception_default($e);
		return false;
	}
}

function require_auth() {
	global $auth;
	if (!$auth->isLoggedIn()) {
		return_error("You must be logged in to view this page.", 403);
		exit(0);
	}
}

function get_user_info() {
	global $auth;
	require_auth();
	if (!isset($_SESSION['_internal_user_info'])) {
		// load info
	}
	return $_SESSION['_internal_user_info'];
}

function get_remember_duration(): int {
	global $config;
	return $config->get('auth_ttl', 60 * 60 * 24 * 1);
}

function send_verification_email(string $selector, string $token, string $email, string $username) {
	$message = "Dear " . $username . ",\r\n You have been registered for a Scrooge account at " . get_base_url() . 
				". If you think this was a mistake you can ignore this email. Otherwise, to activate this account visit: " . 
				get_base_url() . "/auth/verify.php?selector=" . urlencode($selector) . '&token=' . urlencode($token);
	send_email($email, "You have been registered for a Scrooge account", $message);
}

function send_reset_email(string $selector, string $token, string $email, string $username) {
	$message = "Dear " . $username . ",\r\n You have requested a password reset. To do so, visit " . get_base_url() .
	"/auth/reset.php?selector=" . urlencode($selector) . '&token=' . urlencode($token);
	send_email($email, "Scrooge Password Reset", $message);
}

function handle_exception_default(Exception $e) {
	try {
		throw $e;
	} catch (Delight\Auth\TooManyRequestsException $e) {
		return_error('Too many requests, please try again later.', 500);
	} catch (Delight\Auth\TokenExpiredException $e) {
		return_error("Token Expired, sending new verification email.", 500);
	} catch (Delight\Auth\InvalidSelectorTokenPairException $e) {
		return_error("Invalid tokenpair.", 412);
	} catch (Delight\Auth\ResetDisabledException $e) {
		return_error('Password resets are disabled.', 403);
	} catch (Delight\Auth\InvalidEmailException $e) {
		return_error("The email you provided is invalid.", 400);
	} catch (Delight\Auth\InvalidPasswordException $e) {
	    return_error('Invalid password.', 422);
	} catch (Delight\Auth\UserAlreadyExistsException $e) {
	    return_error('User already exists.', 422);
	} catch (Delight\Auth\UserAlreadyExistsException $e) {
		return_error("User has already been verified.", 412);
	} catch (Delight\Auth\NotLoggedInException $e) {
		return_error("You are not currently logged in.", 403);
	} catch (Exception $e) {
		return_error($e->getMessage(), 500);
	}
}

?>