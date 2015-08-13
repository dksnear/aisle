
---- path G:/a-projects/aisle/detector/doc/mysql/insert.sql

---- numerical
---- datetime
---- string
---- binary


-- USE `AISLE_DETECTOR_DB`;

INSERT INTO `T_NUMERIC` (

`c_tinyint`,
`c_smallint`,
`c_mediumint`,
`c_int`,
`c_bigint`,
`c_float`,
`c_double`,
`c_decimal` ) 

VALUES  (-128,-32768,-8388608,-2147483648,-9223372036854775808,'-3.402823466E+38','1.7976931348623157E+308',1111.11),
		(127,32767,8388607,2147483647,9223372036854775807,'1.175494351E-38','2.2250738585072014E-308',1111.11);

INSERT INTO `T_DATETIME` (

`c_date`,
`c_time`,
`c_year`,
`c_datetime`,
`c_timestamp` ) 

VALUES  ('1000-01-01','-838:59:59','1901','1000-01-01 00:00:00',NULL),
		('9999-12-31','838:59:59','2155','9999-12-31 23:59:59',NULL);

INSERT INTO `T_STRING` (

`c_char`,
`c_varchar`,
`c_tinytext`,
`c_text`,
`c_mediumtext`,
`c_logtext`,
`c_enum`,
`c_set` ) 

VALUES ('1111','1111','1111','1111','1111','11111','e2','s1,s2');

INSERT INTO `T_BINARY` (

`c_bit`,
`c_binary`,
`c_varbinary`,
`c_tinyblob`,
`c_blob`,
`c_mediumblob`,
`c_longblob` ) 

VALUES (b'0','11','1111','this is tinyblob!','this is blob!','this is medium blob!','this is large blob!');



