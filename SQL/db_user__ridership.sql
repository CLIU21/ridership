create user `ridership`@`localhost` IDENTIFIED VIA mysql_native_password USING PASSWORD('local-pw-ridership');

GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, CREATE VIEW, DROP, INDEX, ALTER ON `ridership`.* TO `ridership`@`localhost`;
