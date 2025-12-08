CREATE OR REPLACE VIEW ridership_data_view_has_iep AS
SELECT id
, data_month
, last_name
, first_name
, card_number
, scan_day
, scan_time
, scan_hours
, student_id
, district
, service_type
, EXISTS(SELECT * from iep_data as I WHERE R.data_month = I.data_month and R.service_type = I.service_type and R.student_id = I.student_id and I.is_active = 1) AS has_iep
FROM ridership_data as R
WHERE is_active = 1
and student_id <> ""
;
