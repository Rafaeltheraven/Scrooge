
BEGIN;

CREATE TABLE IF NOT EXISTS groups (
	id SERIAL PRIMARY KEY,
	owner INTEGER references users(id)
);

CREATE TABLE IF NOT EXISTS group_members (
	id SERIAL PRIMARY KEY,
	group_id INTEGER references groups(id),
	member_id INTEGER references users(id)
);

CREATE TABLE IF NOT EXISTS group_payments (
	id SERIAL PRIMARY KEY,
	creditor INTEGER references group_members(id)
	amount INTEGER NOT NULL,
	description VARCHAR(255) NOT NULL,
	debitor INTEGER references group_members(id)
	datetime DATETIME NOT NULL
);

COMMIT;