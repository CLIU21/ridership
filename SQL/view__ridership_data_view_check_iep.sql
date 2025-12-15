CREATE OR REPLACE VIEW ridership_data_view_check_iep AS
SELECT data_month
, last_name
, first_name
, scan_day
, diff_hours
, service_name
, service_code
, record_count
, student_id
, district
, service_type
, EXISTS(
	SELECT * from iep_data as I
	WHERE R.data_month = I.data_month
	and R.service_type = I.service_type
	and R.student_id = I.student_id
	and I.is_active = 1
) AS has_iep
FROM ridership_data_view_trips as R
;
