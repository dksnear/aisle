--#query#--
---- path G:/a-projects/aisle/detector/doc/mysql/select.sql

---- numerical
---- datetime
---- string
---- binary

USE `AISLE_DETECTOR_DB`;

SELECT 

`c_tinyint`,
`c_smallint`,
`c_mediumint`,
`c_int`,
`c_bigint`,
`c_float`,
`c_double`,
`c_decimal` FROM `T_NUMERIC` LIMIT 5;

SELECT 

`c_date`,
`c_time`,
`c_year`,
`c_datetime`,
`c_timestamp` FROM `T_DATETIME` LIMIT 5;

SELECT 

`c_char`,
`c_varchar`,
`c_tinytext`,
`c_text`,
`c_mediumtext`,
`c_logtext`,
`c_enum`,
`c_set` FROM `T_STRING` LIMIT 5;

SELECT 

hex(`c_bit`),
`c_binary`,
`c_varbinary`,
`c_tinyblob`,
`c_blob`,
`c_mediumblob`,
`c_longblob` FROM `T_BINARY` LIMIT 5;



