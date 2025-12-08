CREATE VIEW ridership_data_view AS
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
, student_id
, district
, service_type
from ridership_data
WHERE is_active = 1
and student_id <> ""
GROUP BY data_month, student_id, scan_day
;
