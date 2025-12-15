CREATE OR REPLACE VIEW ridership_data_view AS
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
, has_iep
FROM ridership_data_view_check_iep
WHERE has_iep = 1
;
