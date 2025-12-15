CREATE OR REPLACE VIEW ridership_data_view_trips AS
SELECT data_month
, last_name
, first_name
, scan_day
, diff_hours
, IF(diff_hours > 2.5, 'RoundTrip', 'OneWay') as 'service_name'
, IF(diff_hours > 2.5, 'T2', 'T1') as 'service_code'
, record_count
, student_id
, district
, service_type
FROM (
	SELECT data_month
	, last_name
	, first_name
	, scan_day
	, MIN(scan_hours) AS min_hours
	, MAX(scan_hours) AS max_hours
	, MAX(scan_hours) - MIN(scan_hours) AS diff_hours
	, COUNT(scan_hours) AS record_count
	, student_id
	, district
	, service_type
	FROM ridership_data as R
	WHERE is_active = 1
	and student_id <> ""
	GROUP BY data_month, service_type, student_id, scan_day
) as A
;
