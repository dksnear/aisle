/********************

	Aisle System Table CREATOR
	
	G:/a-projects/_local-servers/server-2/lab/$doc/mysql/all.sql

*********************/

SET NAMES UTF8;

DROP TABLE IF EXISTS `aisle_sys_cache`;
DROP TABLE IF EXISTS `aisle_sys_ex_log`;
DROP TABLE IF EXISTS `aisle_sys_debug_log`;


-- aisle 系统缓存表

CREATE TABLE IF NOT EXISTS `aisle_sys_cache` (
	
	`create_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间戳',
	`last_update_time` INT UNSIGNED DEFAULT 0 COMMENT '记录更新时间戳 单位(秒)', 
	`name` VARCHAR(64) NOT NULL COMMENT '缓存名称',
	`key` VARCHAR(128) NOT NULL COMMENT '缓存键',
	`value` MEDIUMTEXT DEFAULT NULL COMMENT '缓存值', 
	`order` INT DEFAULT 0 COMMENT '键序',
	`expire` INT DEFAULT 0 COMMENT '过期时间 单位(秒)',
	`remark` VARCHAR(512) DEFAULT NULL COMMENT '备注',
	`status` INT UNSIGNED DEFAULT 0 COMMENT '状态',
	
	PRIMARY KEY (`name`,`key`)


) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'aisle system cache table';

-- aisle 系统异常日志表

CREATE TABLE IF NOT EXISTS `aisle_sys_ex_log` (
			
	`id` INT UNSIGNED AUTO_INCREMENT COMMENT '系统标识',
	`create_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间戳',
	`type` VARCHAR(64) DEFAULT NULL COMMENT '异常类别',
	`code` INT DEFAULT -1 COMMENT 'php错误码',
	`file` VARCHAR(512) DEFAULT NULL COMMENT 'php错误文件路径',
	`line` INT DEFAULT -1 COMMENT 'php错误行号',
	`msg` VARCHAR(2048) DEFAULT NULL COMMENT 'php错误消息|sql错误消息',
	`trace` MEDIUMTEXT DEFAULT NULL COMMENT 'php错误追踪',
	`sql_statement` TEXT DEFAULT NULL COMMENT 'sql查询语句',
	`sql_params` VARCHAR(4096) DEFAULT NULL COMMENT 'sql查询参数',
	`request` TEXT DEFAULT NULL COMMENT '客户端请求信息',
	`client_ip` VARCHAR(256) DEFAULT NULL COMMENT '客户端ip(可能包含各层代理ip)',
	`remark` VARCHAR(512) DEFAULT NULL COMMENT '备注',
	`status` INT UNSIGNED DEFAULT 0 COMMENT '状态',
	
	PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'aisle system ex log table';


-- aisle 系统调试日志表

CREATE TABLE IF NOT EXISTS `aisle_sys_debug_log`(
	
	`id` INT UNSIGNED AUTO_INCREMENT COMMENT '系统标识',
	`create_time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间戳',
	`sketch` VARCHAR(128) COMMENT '简述',
	`content` VARCHAR(4096) COMMENT '内容',
	PRIMARY KEY (`id`)
	
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'aisle system debug log table';