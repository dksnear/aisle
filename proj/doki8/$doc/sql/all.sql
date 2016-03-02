/********************

	doki8_grab DATABASE CREATOR
	
	G:/a-projects/_local-servers/server-2/lab/project/doki8/$doc/sql/all.sql

*********************/

SET NAMES UTF8;
SET time_zone = '+8:00';

DROP DATABASE IF EXISTS `doki8_grab`;
CREATE DATABASE `doki8_grab` CHARACTER SET UTF8;

USE `doki8_grab`;

-- 用户表

CREATE TABLE IF NOT EXISTS `profile` (

	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间戳',
	`last_update_time` TIMESTAMP NOT NULL COMMENT '记录更新时间戳', 
	`status` INT UNSIGNED DEFAULT 0 COMMENT '状态',
	`name` VARCHAR(50) COMMENT '用户名',
	`nick_name` VARCHAR(127) COMMENT '昵称',
	`rank` INT COMMENT '等级',
	`badges` VARCHAR(127) COMMENT '徽章',
	`points` INT COMMENT '积分统计',
	`last_grab_page_count` INT COMMENT '最后抓取的积分记录总页数',
	`regist_time` TIMESTAMP COMMENT '注册时间',
	`last_visit_time` TIMESTAMP COMMENT '最后访问时间',
	
	PRIMARY KEY (`name`)


) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'user profile table';


-- 文章表

CREATE TABLE IF NOT EXISTS `article` (

	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间戳',
	`last_update_time` TIMESTAMP NOT NULL COMMENT '记录更新时间戳', 
	`status` INT UNSIGNED DEFAULT 0 COMMENT '状态',
	`id` INT COMMENT '文章标识',
	`author` VARCHAR(50) COMMENT '文章发布人用户名',
	`title` VARCHAR(250) COMMENT '文章标题',
	`cast_tags` INT COMMENT '阵容标签',
	`category_tags` INT UNSIGNED DEFAULT 0 COMMENT '分类标签',
	`category_desc` VARCHAR(512) COMMENT '分类描述',
	`comment_count` INT COMMENT '评论统计',
	`view_count` INT COMMENT '查看统计', 
	`publish_time` TIMESTAMP COMMENT '发布时间',
	
	PRIMARY KEY (`id`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'article table';

-- 积分记录表

CREATE TABLE IF NOT EXISTS `point_history` (

	`create_time` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '记录创建时间戳',
	`last_update_time` TIMESTAMP NOT NULL COMMENT '记录更新时间戳', 
	`status` INT UNSIGNED DEFAULT 0 COMMENT '状态',
	`user_name` VARCHAR(50) COMMENT '记录用户名',
	`record_num` INT COMMENT '用户记录编号',
	`article_id` INT COMMENT '文章标识',
	`point` INT DEFAULT 0 COMMENT '积分变动',
	`desc` VARCHAR(127) COMMENT '记录描述',
	`record_time` TIMESTAMP COMMENT '记录时间',
	
	PRIMARY KEY (`user_name`,`record_num`)


) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'point history table';

-- 字典表

CREATE TABLE IF NOT EXISTS `bit_dict`(
	
	`status` INT UNSIGNED DEFAULT 0 COMMENT '状态',
	`group` VARCHAR(128) NOT NULL COMMENT '组名',
	`key` INT UNSIGNED NOT NULL COMMENT '键',
	`value` VARCHAR(256) COMMENT '值',
	`desc` VARCHAR(512) COMMENT '描述',
	
	PRIMARY KEY (`group`,`key`)
	

) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'bit dictionary table';

insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','1','drama','日剧');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','2','online','在线日剧');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','4','taiga','大河剧');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','8','jidaigeki','时代剧');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','16','movie','电影');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','32','music','音乐');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','64','shicho','收视率');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','128','master','站长');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','256','contributor','投稿');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','512','story','剧情');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','1024','love','爱情');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','2048','medical','医疗');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','4096','suspense','悬疑');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','8192','criminal','罪案');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','16384','school','校园');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','32768','horror','恐怖');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','65536','documentary','记录');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','131072','variety','综艺');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','262144','now','最新');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','524288','sp','特别篇');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','1048576','p','P');
insert into `bit_dict`(`group`,`key`,`value`,`desc`) value('article_category','2097152','rare','稀有');


-- 用户关系表 #droped

-- CREATE TABLE IF NOT EXISTS `user_relation` (

	-- `user_name` VARCHAR(50) COMMENT '用户名',
	-- `relation_user_name` VARCHAR(50) COMMENT '关联用户名',
	-- `relation` INT UNSIGNED DEFAULT 0 COMMENT '关系类型 好友(1) 我关注的人(2) 关注我的人(4)',
	
	-- PRIMARY KEY (`user_name`,`relation_user_name`)


-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT 'user relation relative table';


-- 交易统计
-- select `ar`.`title`,`ph`.*,`ar`.`comment_count` as `comments`,`ar`.`view_count` as `views`,`ar`.`publish_time` from
-- (select `article_id` as `id`,sum(`point`) as `sum`,avg(`point`) as `avg`,count(1) as `count`,group_concat(distinct `point`) as `probable` from `point_history` where `point`>0 and `article_id`>0 and `user_name`='dksnear' group by `article_id`) as `ph`
-- join `article` as `ar` on `ar`.`id`=`ph`.`id` order by `ph`.`sum` desc;

-- 用户统计
-- select `p`.*,`a`.`aticle_count`,`ph1`.`comment_times`,`ph2`.`subject_times`,`ph3`.`login_times`,`ph4`.`cost_times`,`ph4`.`costs` from 
-- (select `name`,`nick_name`,`rank`,`badges`,`regist_time`,`points` from `profile` order by `points` desc limit 0,5) as `p`
-- left join (select `author`,count(1) as 'aticle_count' from `article` group by `author`) as `a` on `a`.`author`=`p`.`name`
-- join (select `user_name`,count(1) as 'comment_times' from `point_history` where `desc`='评论奖励' group by `user_name` ) as `ph1` on `ph1`.`user_name` = `p`.`name`
-- join (select `user_name`,count(1) as 'subject_times' from `point_history` where `desc`='回复主题奖励' group by `user_name` ) as `ph2` on `ph2`.`user_name` = `p`.`name`
-- join (select `user_name`,count(1) as 'login_times' from `point_history` where `desc`='每日登录奖励' group by `user_name` ) as `ph3` on `ph3`.`user_name` = `p`.`name`
-- join (select `user_name`,count(1) as 'cost_times',sum(`point`) as 'costs' from `point_history` where `point`<0 group by `user_name` ) as `ph4` on `ph4`.`user_name` = `p`.`name`
-- order by `p`.`points` desc;