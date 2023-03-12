
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
	creditor INTEGER references group_members(id),
	amount MONEY NOT NULL,
	description VARCHAR(255) NOT NULL,
	datetime DATETIME NOT NULL
);

CREATE TABLE IF NOT EXISTS group_payments_debtor (
	payment_id INTEGER references group_payments(id),
	user_id INTEGER references group_members(member_id), 
	payback BOOL DEFAULT false
);
COMMIT;