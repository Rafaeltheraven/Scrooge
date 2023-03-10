BEGIN

CREATE TABLE IF NOT EXISTS user_info (
	user_id INTEGER PRIMARY KEY references users(id),
	full_name VARCHAR(100) NOT NULL DEFAULT "",
	IBAN VARCHAR(34) NOT NULL DEFAULT "",
	BIC VARCHAR(12) NOT NULL DEFAULT "",
	created_by INTEGER references users(id)
);

CREATE TABLE IF NOT EXISTS user_pending (
	user_id INTEGER references user_info(user_id)
);

COMMIT;