CREATE OR REPLACE VIEW ridership_data_view AS
SELECT data_month
, last_name
, first_name
, card_number
, scan_day
, diff_hours
, IF(diff_hours > 2.5, 'RoundTrip', 'OneWay') as 'service_name'
, IF(diff_hours > 2.5, 'T2', 'T1') as 'service_code'
, record_count
, student_id
, district
, service_type
, EXISTS(SELECT * from iep_data as I WHERE A.data_month = I.data_month and A.service_type = I.service_type and A.student_id = I.student_id and I.is_active = 1) AS has_iep
FROM (
	SELECT data_month
	, last_name
	, first_name
	, card_number
	, scan_date
	, scan_day
	-- , scan_time
	, MIN(scan_hours) AS min_hours
	, MAX(scan_hours) AS max_hours
	, MAX(scan_hours) - MIN(scan_hours) AS diff_hours
	, COUNT(scan_hours) AS record_count
	, student_id
	, district
	, service_type
	from ridership_data
	WHERE is_active = 1
	and student_id <> ""
	GROUP BY data_month, service_type, student_id, scan_day
) as A
GROUP BY data_month, service_type, student_id, scan_day
;
