create table iep_data (
	id				INT(11)			AUTO_INCREMENT PRIMARY KEY,
    data_month		VARCHAR(255)	NOT NULL,
    service_type	VARCHAR(255)	NOT NULL,
	student_id		VARCHAR(255)	NOT NULL,
    is_active		TINYINT(1)		DEFAULT 0
);

ALTER TABLE iep_data ADD INDEX (data_month, is_active, service_type, student_id);
