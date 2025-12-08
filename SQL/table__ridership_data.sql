create table ridership_data (
	id				INT(11)			AUTO_INCREMENT PRIMARY KEY,
    data_month		VARCHAR(255)	NOT NULL,
    last_name		VARCHAR(255)	NOT NULL,
    first_name		VARCHAR(255)	NOT NULL,
    card_number		VARCHAR(255)	NOT NULL,
    scan_date		VARCHAR(255)	NOT NULL,
    scan_day		VARCHAR(255)	NOT NULL,
    scan_time		VARCHAR(255)	NOT NULL,
    scan_hours		DOUBLE			NOT NULL,
    student_id		VARCHAR(255)	NOT NULL,
    district		VARCHAR(255)	NOT NULL,
    service_type	VARCHAR(255)	NOT NULL,
    is_active		TINYINT(1)		DEFAULT 0
);

ALTER TABLE ridership_data ADD INDEX (data_month, is_active, service_type, student_id);
