-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_agenda` (
  `agenda_id` int(10) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `is_limited` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `groups` varchar(255) NOT NULL,
  `content` text,
  `start_date` date default '0000-00-00',
  `end_date` date default '0000-00-00',
  `is_time_related` tinyint(3) default '0',
  `show_period` int(11) default '0',  
  PRIMARY KEY  (`agenda_id`),
  KEY `course_id` (`course_id`)
);
-- </query>
-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_attendance` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `at_period` int(11) NOT NULL,
  `at_date` date NOT NULL,
  `at_status` tinyint(4) default '0',
  PRIMARY KEY  (`id`),
  KEY `attend_index` (`course_id`,`user_id`,`at_period`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_attendance_periods` (
  `id` int(11) NOT NULL default '0',
  `period_begin` time NOT NULL,
  `period_end` time NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_backups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  `backupdate` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_cb_assoc` (
  `id` int(11) NOT NULL auto_increment,
  `field_name` varchar(100) NOT NULL,
  `cb_field_id` int(11) NOT NULL,
  `cb_assoc` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `cb_assoc` (`cb_assoc`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_certificate_prints` (
  `id` int(11) NOT NULL auto_increment,
  `uniq_id` varchar(10) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL default '0',
  `course_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL default '0',
  `crtf_date` datetime NOT NULL,
  `crtf_id` int(11) NOT NULL,
  `crtf_text` text NOT NULL,
  `last_printed` datetime NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(25) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `quiz_name` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_id` (`uniq_id`),
  UNIQUE KEY `user_id` (`user_id`,`role_id`,`course_id`,`crtf_id`,`quiz_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_certificate_users` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `crt_option` int(11) NOT NULL,
  `crt_date` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `cu_index` (`course_id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_certificates` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL default '0',
  `course_id` int(11) NOT NULL,
  `published` int(11) NOT NULL default '1',
  `file_id` int(11) NOT NULL,
  `crtf_name` varchar(100) NOT NULL,
  `crtf_text` text NOT NULL,
  `text_x` int(11) NOT NULL,
  `text_y` int(11) NOT NULL,
  `text_size` int(11) NOT NULL,
  `crtf_type` int(11) NOT NULL,
  `crtf_align` int(11) NOT NULL default '0',
  `crtf_shadow` int(11) NOT NULL default '0',
  `crtf_font` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_chat_history` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recv_id` int(11) NOT NULL,
  `user_message` text,
  `mes_time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `history_index` (`course_id`,`group_id`,`user_id`,`recv_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_chat_users` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time_enter` datetime default NULL,
  `time_post` datetime NOT NULL,
  `chat_option` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `chat_users_index` (`course_id`,`group_id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_conference_config` (
  `course_id` int(11) NOT NULL,
  `booking` char(1) NOT NULL,
  PRIMARY KEY  (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_conference_doc` (
  `doc_id` int(11) NOT NULL auto_increment,
  `course_id` int(11) default NULL,
  `owner_id` int(11) default NULL,
  `upload_type` tinyint(1) NOT NULL default '0',
  `filename` varchar(100) default NULL,
  `file_id` int(11) NOT NULL,				  
  PRIMARY KEY  (`doc_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_conference_period` (
  `p_id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL default '0',
  `from_time` int(11) default NULL,
  `to_time` int(11) default NULL,
  `public` char(1) default NULL,
  `p_name` varchar(100) NOT NULL,
  `p_description` text NOT NULL,
  PRIMARY KEY  (`p_id`),
  KEY `course_id` (`course_id`,`teacher_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_conference_records` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `record_name` varchar(70) default NULL,
  `session_name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `user_id` int(11) NOT NULL,
  `published` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_conference_usr` (
  `user_id` int(11) NOT NULL,
  `p_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`p_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_config` (
  `lms_config_var` varchar(50) NOT NULL default '',
  `lms_config_value` text,
  PRIMARY KEY  (`lms_config_var`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_course_cats` (
  `id` int(11) NOT NULL auto_increment,
  `c_category` varchar(50) NOT NULL,
  `parent` int(11) NOT NULL,
  `restricted` int(11) NOT NULL default '0',
  `groups` varchar(255) NOT NULL,
  `lesson_type` int(11) default '0',
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_course_cats_config` (
  `id` int(11) NOT NULL,
  `cat_name` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_course_level` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `cat_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_courses` (
  `id` int(11) NOT NULL auto_increment,
  `course_name` varchar(100) NOT NULL,
  `name_alias` varchar(100) NOT NULL,
  `course_description` text,
  `course_sh_description` text,
  `owner_id` int(11) NOT NULL,
  `cat_id` int(11) default NULL,
  `published` tinyint(4) default '0',
  `publish_start` tinyint(4) default '0',
  `start_date` date NOT NULL,
  `publish_end` tinyint(4) default '0',
  `end_date` date NOT NULL,
  `metadesc` text NOT NULL,
  `metakeys` text NOT NULL,
  `language` int(11) NOT NULL,
  `self_reg` tinyint(4) NOT NULL,
  `add_forum` tinyint(4) default '0',
  `add_chat` tinyint(4) default '0',
  `add_hw` tinyint(4) default '1',
  `add_attend` tinyint(4) default '1',
  `paid` tinyint(4) default '0',
  `course_price` decimal(12,5) default NULL,
  `spec_reg` tinyint(4) default '0',
  `gid` tinytext NOT NULL,
  `params` text NOT NULL,
  `sec_cat` text,
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_courses_backups` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `backupdate` datetime default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_courses_template` (
  `id` int(11) NOT NULL auto_increment,
  `templ` varchar(255) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_disc_c_usage_stats` (
  `id` int(11) NOT NULL auto_increment,
  `coupon_code` varchar(32) default NULL,
  `coupon_id` int(10) unsigned default NULL,
  `payment_id` int(10) unsigned NOT NULL,
  `user_id` int(11) unsigned default NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_discount_coupons` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `code` varchar(32) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `coupon_type` tinyint(1) NOT NULL,
  `discount_type` tinyint(4) NOT NULL,
  `value` decimal(11,2) NOT NULL,
  `subscriptions` text NOT NULL,
  `usergroups` text,
  `users` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `removed` tinyint(1) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_discounts` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  `discount_type` tinyint(4) NOT NULL,
  `value` decimal(11,2) NOT NULL,
  `subscriptions` text NOT NULL,
  `usergroups` text,
  `users` text NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_documents` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `folder_flag` int(11) NOT NULL default '0',
  `parent_id` int(11) default '0',
  `doc_name` varchar(100) NOT NULL,
  `doc_description` text,
  `ordering` int(11) NOT NULL default '0',
  `published` int(11) NOT NULL,
  `publish_start` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `publish_end` int(11) NOT NULL,
  `end_date` date NOT NULL,
  `is_time_related` tinyint(3) default '0',
  `show_period` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_documents_perms` (
  `doc_id` INT NOT NULL ,
  `role_id` INT NOT NULL ,
  `p_view` TINYINT NOT NULL ,
  `p_viewall` TINYINT NOT NULL ,
  `p_order` TINYINT NOT NULL ,
  `p_publish` TINYINT NOT NULL ,
  `p_manage` TINYINT NOT NULL ,
  PRIMARY KEY ( `doc_id` , `role_id` )
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_documents_view` (
  `course_id` int(11) NOT NULL,
  `doc_id` int(11) NOT NULL,
  PRIMARY KEY  (`course_id`,`doc_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_documents_zip` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `zip_name` varchar(255) NOT NULL,
  `zip_srv_name` varchar(255) NOT NULL,
  `zip_folder` varchar(255) NOT NULL,
  `startup_file` varchar(255) NOT NULL,
  `count_files` int(11) default '0',
  `zip_size` int(11) default '0',
  `zipfile_size` int(11) NOT NULL,
  `upload_time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_dropbox` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `recv_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `drp_type` int(11) NOT NULL default '1',
  `drp_mark` int(11) NOT NULL default '0',
  `drp_corrected` int(11) NOT NULL,
  `drp_time` datetime NOT NULL,
  `drp_name` varchar(100) NOT NULL,
  `drp_description` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `dropbox_index` (`course_id`,`owner_id`,`recv_id`,`file_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_email_notifications` (
  `id` int(11) NOT NULL default '0',
  `learner_template` int(11) default NULL,
  `manager_template` int(11) default NULL,
  `selected_manager_roles` varchar(255) NOT NULL,
  `learner_template_disabled` tinyint(3) NOT NULL,
  `manager_template_disabled` tinyint(3) NOT NULL,
  `disabled` tinyint(3) default NULL,
  KEY `learner_template` (`learner_template`),
  KEY `manager_template` (`manager_template`),
  KEY `selected_manager_roles` (`selected_manager_roles`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_email_templates` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(60) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `notification_type` int(11) NOT NULL,
  `body_html` text NOT NULL,
  `body_text` text NOT NULL,
  `disabled` tinyint(3) default NULL,
  PRIMARY KEY  (`id`)
) AUTO_INCREMENT = 101;
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_file_types` (
  `filetype` varchar(5) NOT NULL,
  PRIMARY KEY  (`filetype`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_files` (
  `id` int(11) NOT NULL auto_increment,
  `file_name` varchar(100) NOT NULL,
  `file_srv_name` varchar(100) NOT NULL,
  `owner_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_forum_details` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) default '0',
  `board_type` tinyint(4) default '1',
  `group_id` int(11) NOT NULL default '0',
  `ID_GROUP` int(11) default '0',
  `ID_CAT` int(11) default '0',
  `ID_BOARD` int(11) default '0',
  `is_active` tinyint(4) NOT NULL default '0',
  `need_update` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `forum_index` (`course_id`,`group_id`,`is_active`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_forums` (
  `id` int(11) NOT NULL auto_increment,
  `parent_forum` int(11) NOT NULL default '0',
  `published` tinyint(4) NOT NULL default '1',
  `forum_level` tinyint(4) default '0',
  `user_level` tinyint(4) NOT NULL default '0',
  `moderated` tinyint(4) NOT NULL default '1',
  `need_update` tinyint(4) NOT NULL default '0',
  `forum_access` tinyint(4) NOT NULL default '0',
  `forum_permissions` varchar(255) NOT NULL,
  `forum_moderators` varchar(255) NOT NULL,
  `forum_name` varchar(255) NOT NULL,
  `forum_desc` text NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_gqp_cats` (
  `id` int(11) NOT NULL auto_increment,
  `c_category` varchar(50) NOT NULL,
  `parent` int(11) NOT NULL,
  `restricted` int(11) NOT NULL default '0',
  `groups` varchar(255) NOT NULL,
  `lesson_type` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_gqp_levels` (
  `id` int(11) NOT NULL auto_increment,
  `quest_id` int(11) default NULL,
  `cat_id` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `quest_id` (`quest_id`,`cat_id`,`level`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_gradebook` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `gbi_id` int(11) NOT NULL,
  `gb_points` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `gb_index` (`course_id`,`user_id`,`gbi_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_gradebook_cats` (
  `id` int(11) NOT NULL auto_increment,
  `gb_category` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_gradebook_items` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `gbc_id` int(11) NOT NULL,
  `gbi_name` varchar(100) NOT NULL,
  `gbi_description` text NOT NULL,
  `gbi_date` date NOT NULL,
  `gbi_points` int(11) NOT NULL,
  `gbi_option` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `gbi_index` (`course_id`,`gbc_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_gradebook_lpaths` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `learn_path_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `course_id` (`course_id`,`learn_path_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_gradebook_scale` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `scale_name` varchar(10) NOT NULL,
  `min_val` int(11) NOT NULL,
  `max_val` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_homework` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL default '0',
  `is_limited` int(11) NOT NULL default '0',
  `hw_name` varchar(100) NOT NULL,
  `groups` varchar(255) NOT NULL,
  `hw_description` text NOT NULL,
  `hw_shortdescription` text NOT NULL,
  `post_date` date default NULL,
  `end_date` date default NULL,
  `is_time_related` tinyint(3) default '0',
  `show_period` int(11) default '0',
  `published` tinyint(1) default '1',
  `activity_type` tinyint(4) default '1',
  `graded_activity` tinyint(1) default '0',
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_homework_results` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `hw_id` int(11) NOT NULL,
  `hw_status` int(11) NOT NULL,
  `hw_date` datetime NOT NULL,
  `write_text` text NOT NULL,
  `file_id` int(11) NOT NULL,
  `grade` varchar(255) NOT NULL,
  `comments` text,
  PRIMARY KEY  (`id`),
  KEY `hwr_index` (`course_id`,`user_id`,`hw_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_languages` (
  `id` int(11) NOT NULL auto_increment,
  `lang_name` varchar(20) NOT NULL,
  `published` int(11) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `lang_index` (`published`,`lang_name`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_learn_path_conds` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `lpath_id` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  `ref_step` int(11) NOT NULL,
  `cond_type` int(11) NOT NULL,
  `cond_value` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `lpc_index` (`course_id`,`lpath_id`,`step_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_learn_path_grades` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `lpath_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_points` int(11) NOT NULL,
  `user_time` int(11) NOT NULL,
  `user_status` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `lpr_index` (`course_id`,`lpath_id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_learn_path_prerequisites` (
  `lpath_id` int(11) NOT NULL,
  `req_id` int(11) NOT NULL,
  `time_minutes` int(11) default NULL,
  UNIQUE KEY `lpath_id` (`lpath_id`,`req_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_learn_path_results` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `lpath_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_points` int(11) NOT NULL,
  `user_time` int(11) NOT NULL,
  `user_status` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `lpr_index` (`course_id`,`lpath_id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_learn_path_step_quiz_results` (
  `id` int(11) NOT NULL auto_increment,
  `result_id` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  `stu_quiz_id` int(11) NOT NULL,
  `start_id` int(11) NOT NULL,
  `unique_id` varchar(32) default NULL,
  PRIMARY KEY  (`id`),
  KEY `lpsqr_index` (`result_id`,`step_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_learn_path_step_results` (
  `id` int(11) NOT NULL auto_increment,
  `result_id` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  `step_status` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `lpsr_index` (`result_id`,`step_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_learn_path_step_types` (
  `id` int(11) NOT NULL,
  `step_type` varchar(25) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_learn_path_steps` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `lpath_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `step_type` int(11) default '0',
  `parent_id` int(11) default '0',
  `step_name` varchar(100) default NULL,
  `step_shortdescription` text NOT NULL,
  `step_description` text,
  `ordering` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `lps_index` (`course_id`,`lpath_id`,`parent_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_learn_paths` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `lp_type` int(11) NOT NULL default '0',
  `lpath_name` varchar(100) NOT NULL,
  `lpath_shortdescription` text NOT NULL,
  `lpath_description` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) default '0',
  `lp_params` text NOT NULL,
  `is_time_related` tinyint(3) default '0',
  `show_period` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_links` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `link_name` varchar(100) default NULL,
  `link_href` varchar(255) default NULL,
  `link_description` text NOT NULL,
  `link_type` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` int(11) NOT NULL,
  `is_time_related` tinyint(3) default '0',
  `show_period` int(11) default '0',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_local_menu` (
  `course_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `user_access` tinyint(4) NOT NULL,
  PRIMARY KEY  (`course_id`,`menu_id`,`user_access`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_maintenance_log` (
  `ID` int(11) NOT NULL auto_increment,
  `log_time` datetime NOT NULL,
  `log_action` varchar(100) NOT NULL,
  `log_result` text NOT NULL,
  PRIMARY KEY  (`ID`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_menu` (
  `id` int(11) NOT NULL auto_increment,
  `lang_var` varchar(50) NOT NULL,
  `image` varchar(30) NOT NULL,
  `task` varchar(20) NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(4) NOT NULL,
  `is_separator` tinyint(4) NOT NULL,
  `user_access` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `menu_index` (`user_access`,`published`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_message_configuration` (
  `message_conf` varchar(255) NOT NULL,
  `message_value` text NOT NULL,
  PRIMARY KEY  (`message_conf`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_messagelist` (
  `id` int(11) NOT NULL auto_increment,
  `pm_name` varchar(100) NOT NULL,
  `pm_email` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_messages` (
  `id` int(11) NOT NULL auto_increment,
  `sender_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL default '0',
  `subject` varchar(255) default NULL,
  `data` datetime default NULL,
  `message` text,
  `file` int(11) default '0',
  `del` tinyint(4) default '0',
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_messages_to` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_read` tinyint(4) default '0',
  `del` tinyint(4) default '0',
  PRIMARY KEY  (`id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `course_id` int(10) unsigned default '0',
  `scorm_name` varchar(255) default NULL,
  `scorm_package` int(11) default '0',
  `summary` text,
  `version` varchar(9) default NULL,
  `maxgrade` double NOT NULL default '0',
  `grademethod` tinyint(2) NOT NULL default '0',
  `maxattempt` bigint(10) NOT NULL default '1',
  `updatefreq` tinyint(1) unsigned NOT NULL default '0',
  `md5hash` varchar(32) default NULL,
  `launch` bigint(10) unsigned NOT NULL default '0',
  `skipview` tinyint(1) unsigned NOT NULL default '1',
  `hidebrowse` tinyint(1) NOT NULL default '0',
  `hidetoc` tinyint(1) NOT NULL default '0',
  `hidenav` tinyint(1) NOT NULL default '0',
  `auto` tinyint(1) unsigned NOT NULL default '0',
  `popup` tinyint(1) unsigned NOT NULL default '0',
  `params` text,
  `width` bigint(10) unsigned NOT NULL default '100',
  `height` bigint(10) unsigned NOT NULL default '600',
  `timemodified` bigint(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `lms_n_scorm_course` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm_lib` (
  `lib_id` INT NOT NULL ,
  `lpath_id` INT NOT NULL ,
  `course_id` INT NOT NULL ,
  `lib_n_scorm_id` INT NOT NULL ,
  `lpath_n_scorm_id` INT NOT NULL ,
  `scorm_package` INT NOT NULL ,
  UNIQUE KEY `lib_id` (`lib_id`,`lpath_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm_scoes` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `scorm` bigint(10) unsigned NOT NULL default '0',
  `manifest` varchar(255) default NULL,
  `organization` varchar(255) default NULL,
  `parent` varchar(255) default NULL,
  `identifier` varchar(255) default NULL,
  `launch` varchar(255) default NULL,
  `scormtype` varchar(5) default NULL,
  `title` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `lms_n_scorm_sco_scorm` (`scorm`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm_scoes_data` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `scoid` bigint(10) unsigned NOT NULL default '0',
  `name` varchar(255) default NULL,
  `value` text,
  PRIMARY KEY  (`id`),
  KEY `lms_n_scorm_sd_sco` (`scoid`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm_scoes_track` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `userid` bigint(10) unsigned NOT NULL default '0',
  `scormid` bigint(10) NOT NULL default '0',
  `scoid` bigint(10) unsigned NOT NULL default '0',
  `attempt` bigint(10) unsigned NOT NULL default '1',
  `element` varchar(255) default NULL,
  `value` longtext,
  `timemodified` bigint(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `lms_n_scorm_track_un` (`userid`,`scormid`,`scoid`,`attempt`,`element`),
  KEY `lms_n_scorm_track_sco` (`scoid`),
  KEY `lms_n_scorm_track_el` (`element`),
  KEY `lms_n_scorm_track_scorm` (`scormid`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm_seq_mapinfo` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `scoid` bigint(10) unsigned NOT NULL default '0',
  `objectiveid` bigint(10) unsigned NOT NULL default '0',
  `targetobjectiveid` bigint(10) unsigned NOT NULL default '0',
  `readsatisfiedstatus` tinyint(1) NOT NULL default '1',
  `readnormalizedmeasure` tinyint(1) NOT NULL default '1',
  `writesatisfiedstatus` tinyint(1) NOT NULL default '0',
  `writenormalizedmeasure` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `lms_n_scorm_seq_map_un` (`scoid`,`id`,`objectiveid`),
  KEY `lms_n_scorm_seq_map_sco` (`scoid`),
  KEY `lms_n_scorm_seq_map_obj` (`objectiveid`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm_seq_objective` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `scoid` bigint(10) unsigned NOT NULL default '0',
  `primaryobj` tinyint(1) NOT NULL default '0',
  `objectiveid` bigint(10) unsigned NOT NULL default '0',
  `satisfiedbymeasure` tinyint(1) NOT NULL default '1',
  `minnormalizedmeasure` float(11,4) unsigned NOT NULL default '0.0000',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `lms_n_scorm_seq_obj_un` (`scoid`,`id`),
  KEY `lms_n_scorm_seq_obj_sco` (`scoid`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm_seq_rolluprulecond` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `scoid` bigint(10) unsigned NOT NULL default '0',
  `rollupruleid` bigint(10) unsigned NOT NULL default '0',
  `operator` varchar(5) default 'noOp',
  `cond` varchar(25) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `lms_n_scorm_seq_rrc_un` (`scoid`,`rollupruleid`,`id`),
  KEY `lms_n_scorm_seq_rrc_sco` (`scoid`),
  KEY `lms_n_scorm_seq_rrc_rr` (`rollupruleid`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm_seq_rulecond` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `scoid` bigint(10) unsigned NOT NULL default '0',
  `ruleconditionsid` bigint(10) unsigned NOT NULL default '0',
  `referencedobjective` varchar(255) default NULL,
  `measurethreshold` float(11,4) NOT NULL default '0.0000',
  `operator` varchar(5) default 'noOp',
  `cond` varchar(30) default 'always',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `lms_n_scorm_seq_rulecond_un` (`id`,`scoid`,`ruleconditionsid`),
  KEY `lms_n_scorm_seq_rulecond_sco` (`scoid`),
  KEY `lms_n_scorm_seq_rulecond_rc` (`ruleconditionsid`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_n_scorm_seq_ruleconds` (
  `id` bigint(10) unsigned NOT NULL auto_increment,
  `scoid` bigint(10) unsigned NOT NULL default '0',
  `conditioncombination` varchar(3) default 'all',
  `ruletype` tinyint(2) unsigned NOT NULL default '0',
  `action` varchar(25) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `lms_n_scorm_seq_ruleconds_un` (`scoid`,`id`),
  KEY `lms_n_scorm_seq_ruleconds_sco` (`scoid`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_notifications` (
  `id` int(11) NOT NULL auto_increment,
  `assigned` int(11) default NULL,
  `mail_address` varchar(200) default NULL,
  `mail_subject` varchar(200) default NULL,
  `mail_body` text,
  `sent` tinyint(4) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_outer_documents` (
  `id` int(11) NOT NULL auto_increment,
  `owner_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `folder_flag` int(11) NOT NULL default '0',
  `parent_id` int(11) default '0',
  `doc_name` varchar(100) NOT NULL,
  `doc_description` text,
  `ordering` int(11) NOT NULL default '0',
  `published` int(11) NOT NULL,
  `publish_start` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `publish_end` int(11) NOT NULL,
  `end_date` date NOT NULL,
  `outdoc_share` tinyint(4) default '0',
  `allow_link` tinyint(4) default '0',
  PRIMARY KEY  (`id`),
  KEY `file_id` (`file_id`,`folder_flag`),
  KEY `owner_id` (`owner_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_page_notices` (
  `id` int(11) NOT NULL auto_increment,
  `usr_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `task` varchar(20) default NULL,
  `doc_id` int(11) NOT NULL,
  `notice` text NOT NULL,
  `data` datetime default NULL,
  PRIMARY KEY  (`id`),
  KEY `usr_id` (`usr_id`,`course_id`,`task`,`doc_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_page_tips` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tip_task` varchar(20) NOT NULL,
  `tip_message` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `course_id` (`course_id`,`user_id`,`tip_task`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_payment_info` (
  `payment_id` INT NOT NULL,
  `user_ip` CHAR( 15 ) NOT NULL,
  `user_alt_ip` CHAR( 15 ) NOT NULL,
  PRIMARY KEY ( `payment_id` )
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_payment_items` (
  `payment_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `item_type` tinyint(4) NOT NULL,
  KEY `payment_id` (`payment_id`,`item_id`,`item_type`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_payments` (
  `id` int(11) NOT NULL auto_increment,
  `payment_type` int(11) NOT NULL default '0',
  `sub_id` int(11) default NULL,
  `proc_id` int(11) default NULL,				  
  `txn_id` varchar(255) default NULL,
  `processor` varchar(255) default NULL,
  `status` varchar(255) default NULL,
  `type` varchar(4) default '0',
  `amount` decimal(12,5) default NULL,
  `cur_code` char(3) default NULL,
  `tax_amount` varchar(255) default NULL,
  `tax2_amount` varchar(255) default NULL,
  `date` datetime default '0000-00-00 00:00:00',
  `user_id` int(11) default '0',
  `checked_out` int(11) default '0',
  `checked_out_time` datetime default '0000-00-00 00:00:00',
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_payments_checksum` (
  `payment_id` int(11) NOT NULL,
  `a1` float NOT NULL,
  `a2` float NOT NULL,
  `a3` float NOT NULL,
  `p1` int(11) NOT NULL,
  `p2` int(11) NOT NULL,
  `p3` int(11) NOT NULL,
  `srt` int(11) NOT NULL,
  KEY `payment_id` (`payment_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_plans` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `published` smallint(6) NOT NULL default '0',
  `p1` mediumint(9) NOT NULL default '0',
  `t1` char(1) NOT NULL default '',
  `p2` mediumint(9) NOT NULL default '0',
  `t2` char(1) NOT NULL default '',
  `p3` mediumint(9) NOT NULL default '0',
  `t3` char(1) NOT NULL default '',
  `src` smallint(6) NOT NULL default '0',
  `sra` smallint(6) NOT NULL default '0',
  `srt` smallint(6) NOT NULL default '0',
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `params` text NOT NULL,
  PRIMARY KEY  (`id`)
);			
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_plans_subscriptions` (
  `subscr_id` int(11) NOT NULL default '0',
  `plan_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`subscr_id`,`plan_id`)
);			
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_plugins` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `element` varchar(30) default NULL,
  `folder` varchar(30) default NULL,
  `ordering` int(11) NOT NULL default '0',
  `published` tinyint(3) NOT NULL default '0',
  `params` text NOT NULL,
  `checked_out` int(11) default NULL,
  `checked_out_time` datetime default NULL,
  `short_description` text,
  PRIMARY KEY  (`id`),
  KEY `folder` (`folder`,`element`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_images` (
  `c_id` int(11) NOT NULL auto_increment,
  `imgs_name` varchar(250) NOT NULL,
  `imgs_id` int(11) default NULL,
  `course_id` int(11) NOT NULL,
  PRIMARY KEY  (`c_id`),
  KEY `course_id` (`course_id`,`imgs_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_languages` (
  `id` int(11) NOT NULL auto_increment,
  `lang_file` varchar(50) default NULL,
  `is_default` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_blank` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_sq_id` int(10) unsigned NOT NULL default '0',
  `c_answer` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`c_id`),
  KEY `c_sq_id` (`c_sq_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_choice` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_sq_id` int(10) unsigned NOT NULL default '0',
  `c_choice_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_sq_id` (`c_sq_id`),
  KEY `c_choice_id` (`c_choice_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_hotspot` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_sq_id` int(10) unsigned NOT NULL default '0',
  `c_select_x` int(10) unsigned NOT NULL default '0',
  `c_select_y` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_sq_id` (`c_sq_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_matching` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_sq_id` int(10) unsigned NOT NULL default '0',
  `c_sel_text` text NOT NULL,
  `c_matching_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_sq_id` (`c_sq_id`),
  KEY `c_matching_id` (`c_matching_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_question` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_stu_quiz_id` int(10) unsigned NOT NULL default '0',
  `c_question_id` int(10) unsigned NOT NULL default '0',
  `c_score` tinyint(4) NOT NULL default '0',
  `c_attempts` int(11) NOT NULL default '0',
  `c_correct` int(11) NOT NULL,
  PRIMARY KEY  (`c_id`),
  KEY `c_stu_quiz_id` (`c_stu_quiz_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_quiz` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_quiz_id` int(10) unsigned NOT NULL default '0',
  `c_student_id` int(11) unsigned NOT NULL default '0',
  `c_total_score` float unsigned NOT NULL default '0',
  `c_total_time` int(10) unsigned NOT NULL default '0',
  `c_date_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `c_passed` tinyint(1) unsigned NOT NULL default '0',
  `unique_id` varchar(32) NOT NULL,
  `allow_review` int(11) NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_student_id` (`c_student_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_quiz_gqp` (
  `start_id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`start_id`,`quest_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_quiz_pool` (
  `start_id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL,
  `ordering` int(11) NOT NULL,
  PRIMARY KEY  (`start_id`,`quest_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_scale` (
  `c_id` int(11) NOT NULL auto_increment,
  `c_sq_id` int(11) NOT NULL,
  `q_scale_id` int(11) NOT NULL,
  `scale_id` int(11) NOT NULL,
  PRIMARY KEY  (`c_id`),
  KEY `c_sq_id` (`c_sq_id`,`q_scale_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_r_student_survey` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_sq_id` int(10) unsigned NOT NULL default '0',
  `c_answer` text NOT NULL,
  PRIMARY KEY  (`c_id`),
  KEY `c_sq_id` (`c_sq_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_results` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_score` int(11) NOT NULL,
  `quiz_max_score` int(11) default '0',
  `user_time` int(11) NOT NULL,
  `quiz_date` datetime NOT NULL,
  `user_passed` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_blank` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_question_id` int(10) unsigned NOT NULL default '0',
  `c_default` varchar(100) NOT NULL,
  PRIMARY KEY  (`c_id`),
  KEY `c_question_id` (`c_question_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_category` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `c_category` varchar(255) NOT NULL default '',
  `c_instruction` text NOT NULL,
  `is_quiz_cat` int(11) NOT NULL default '1',
  PRIMARY KEY  (`c_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_choice` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_choice` text NOT NULL,
  `c_right` char(1) NOT NULL default '0',
  `c_question_id` int(10) unsigned NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_question_id` (`c_question_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_hotspot` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_question_id` int(10) unsigned NOT NULL default '0',
  `c_start_x` int(10) unsigned NOT NULL default '0',
  `c_start_y` int(10) unsigned NOT NULL default '0',
  `c_width` int(10) unsigned NOT NULL default '0',
  `c_height` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_question_id` (`c_question_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_matching` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_question_id` int(10) unsigned NOT NULL default '0',
  `c_left_text` text NOT NULL,
  `c_right_text` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_question_id` (`c_question_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_qtypes` (
  `c_id` int(11) NOT NULL auto_increment,
  `c_qtype` varchar(50) NOT NULL,
  PRIMARY KEY  (`c_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_question` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `c_quiz_id` int(10) unsigned NOT NULL default '0',
  `c_point` tinyint(3) unsigned NOT NULL default '0',
  `c_attempts` tinyint(3) unsigned NOT NULL default '1',
  `c_question` text NOT NULL,
  `c_image` varchar(255) NOT NULL default '',
  `c_type` tinyint(4) NOT NULL default '0',
  `published` int(11) NOT NULL default '1',
  `ordering` int(11) default '0',
  `c_pool` int(11) NOT NULL default '0',
  `c_qcat` int(11) NOT NULL default '0',
  `params` text NOT NULL,
  `c_explanation` text NOT NULL,
  `c_pool_gqp` int(11) NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_quiz_id` (`c_quiz_id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_question_fb` (
  `quest_id` int(11) NOT NULL,
  `choice_id` int(11) NOT NULL,
  `fb_text` text NOT NULL
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_quiz` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `c_category_id` int(11) unsigned NOT NULL default '0',
  `c_user_id` int(11) unsigned NOT NULL default '0',
  `c_author` varchar(255) NOT NULL default '',
  `c_full_score` int(11) default '0',
  `c_title` varchar(255) NOT NULL default '',
  `c_description` text NOT NULL,
  `c_image` varchar(255) NOT NULL default '',
  `c_time_limit` int(10) unsigned NOT NULL default '0',
  `c_min_after` int(10) unsigned NOT NULL default '0',
  `c_passing_score` float unsigned NOT NULL default '0',
  `c_created_time` date NOT NULL default '0000-00-00',
  `c_published` char(1) NOT NULL default '0',
  `c_right_message` text NOT NULL,
  `c_wrong_message` text NOT NULL,
  `c_pass_message` text NOT NULL,
  `c_unpass_message` text NOT NULL,
  `c_enable_review` char(1) NOT NULL default '',
  `c_email_to` tinyint(1) unsigned NOT NULL default '0',
  `c_enable_print` char(1) NOT NULL default '',
  `c_enable_sertif` char(1) NOT NULL default '',
  `c_skin` tinyint(3) unsigned NOT NULL default '0',
  `c_random` tinyint(1) unsigned NOT NULL default '0',
  `c_guest` tinyint(1) unsigned NOT NULL default '0',
  `published` int(11) NOT NULL default '0',
  `c_slide` tinyint(4) NOT NULL default '1',
  `c_language` int(11) NOT NULL,
  `c_certificate` int(11) NOT NULL,
  `c_gradebook` int(11) default '0',
  `params` text NOT NULL,
  `c_resume` TINYINT NOT NULL default '0',
  `c_max_numb_attempts` INT NOT NULL default '0',
  `is_time_related` tinyint(3) default '0',
  `show_period` int(11) default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_user_id` (`c_user_id`),
  KEY `c_author` (`c_author`),
  KEY `c_category_id` (`c_category_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_quiz_gqp` (
  `quiz_id` int(11) NOT NULL,
  `qcat_id` int(11) NOT NULL,
  `items_number` int(11) NOT NULL,
  `orderin` int(11) default NULL,
  PRIMARY KEY  (`quiz_id`,`qcat_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_quiz_gqp_questions` (
  `id` int(11) NOT NULL auto_increment,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `orderin` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_quiz_pool` (
  `quiz_id` int(11) NOT NULL,
  `qcat_id` int(11) NOT NULL,
  `items_number` int(11) NOT NULL,
  PRIMARY KEY  (`quiz_id`,`qcat_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_scale` (
  `c_id` int(11) NOT NULL auto_increment,
  `c_question_id` int(11) default NULL,
  `c_field` varchar(255) NOT NULL,
  `c_type` tinyint(4) default '0',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_question_id` (`c_question_id`,`c_type`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_t_text` (
  `c_id` int(10) unsigned NOT NULL auto_increment,
  `c_blank_id` int(10) unsigned NOT NULL default '0',
  `c_text` varchar(255) NOT NULL default '',
  `ordering` int(11) NOT NULL default '0',
  PRIMARY KEY  (`c_id`),
  KEY `c_blank_id` (`c_blank_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_quiz_templates` (
  `id` int(11) NOT NULL auto_increment,
  `template_name` varchar(250) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_scorm_packages` (
  `id` int(11) NOT NULL auto_increment,
  `owner_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `folder_srv_name` varchar(100) NOT NULL,
  `package_srv_name` varchar(100) NOT NULL,
  `package_user_name` varchar(100) NOT NULL,
  `upload_time` datetime NOT NULL,
  `window_height` varchar(10) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_scorm_sco` (
  `content_id` int(11) NOT NULL,
  `sco_id` int(11) NOT NULL,
  `sco_identifier` varchar(100) default NULL,
  `sco_title` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `sco_time` varchar(20) default NULL,
  KEY `ss_index` (`content_id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_spec_reg_answers` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL default '0',
  `quest_id` int(11) NOT NULL default '0',
  `user_answer` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `course_user` (`course_id`,`user_id`,`role_id`,`quest_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_spec_reg_questions` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL default '0',
  `is_optional` int(11) NOT NULL default '0',
  `ordering` int(11) NOT NULL default '0',
  `default_answer` varchar(255) NOT NULL,
  `course_question` text NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_subs_invoice` (
  `subid` int(11) NOT NULL,
  `filename` varchar(100) NOT NULL,
  PRIMARY KEY  (`subid`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_subscriptions` (
  `id` int(11) NOT NULL auto_increment,
  `sub_name` varchar(100) default NULL,
  `account_type` int(11) default '0',
  `date` datetime default '0000-00-00 00:00:00',
  `published` tinyint(4) default '0',
  `access_days` int(11) default '0',
  `start_date` date default '0000-00-00',
  `end_date` date default '0000-00-00',
  `price` decimal(12,5) default NULL,
  `discount` decimal(12,5) default NULL,
  `sub_descr` text NOT NULL,
  `restricted` int(11) NOT NULL,
  `restricted_groups` varchar(250) default NULL,
  `a1` decimal(12,2) default NULL,
  `a2` decimal(12,2) default NULL,
  `a3` decimal(12,2) default NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_subscriptions_config` (
  `site_name` varchar(150) NOT NULL,
  `site_descr` text NOT NULL,
  `comp_descr` text NOT NULL,
  `comments` text NOT NULL,
  `invoice_descr` text NOT NULL,
  `thanks_text` text NOT NULL,
  `invoice_number` int(11) NOT NULL default '0',
  `mail_subj` varchar(255) NOT NULL,
  `mail_body` text NOT NULL,
  PRIMARY KEY  (`site_name`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_subscriptions_countries` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `code` varchar(5) NOT NULL default '',
  `tax_type` smallint(6) NOT NULL default '0',
  `tax` varchar(255) NOT NULL default '0',
  `published` smallint(6) NOT NULL default '0',
  `list` varchar(255) NOT NULL default '',
  `checked_out` int(11) NOT NULL default '0',
  `checket_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_subscriptions_courses` (
  `sub_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  UNIQUE KEY `sc_index` (`sub_id`,`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_subscriptions_custom` (
  `id` int(11) NOT NULL auto_increment,
  `price` decimal(12,5) default NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_subscriptions_custom_courses` (
  `sub_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  UNIQUE KEY `sc_index` (`sub_id`,`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_subscriptions_procs` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `filename` varchar(255) NOT NULL default '',
  `default_p` tinyint(4) NOT NULL default '0',
  `params` text NOT NULL,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL default '0',
  `published` smallint(6) NOT NULL default '0',
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_topic_items` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) default NULL,
  `topic_id` int(11) default NULL,
  `item_id` int(11) default NULL,
  `item_type` int(11) default NULL,
  `ordering` int(11) default NULL,
  `show` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`,`topic_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_topics` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) default NULL,
  `ordering` int(11) default NULL,
  `name` varchar(100) NOT NULL default '',
  `description` text,
  `published` tinyint(4) default NULL,
  `start_date` date NOT NULL default '0000-00-00',
  `publish_start` tinyint(4) default NULL,
  `end_date` date NOT NULL default '0000-00-00',
  `publish_end` tinyint(4) default NULL,
  `is_time_related` tinyint(3) default '0',
  `show_period` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_track_chat` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `track_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_track_downloads` (
  `id` int(11) NOT NULL auto_increment,
  `doc_id` int(11) default NULL,
  `user_id` int(11) NOT NULL,
  `track_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_track_hits` (
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `page_id` tinyint(4) default NULL,
  `track_time` datetime NOT NULL
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_track_learnpath_stats` (
  `id` int(11) NOT NULL auto_increment,
  `unique_id` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lpath_id` int(11) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `user_points` int(11) NOT NULL,
  `user_status` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_track_learnpath_steps` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `start_id` int(11) NOT NULL,
  `lpath_id` int(11) NOT NULL,
  `step_id` int(11) NOT NULL,
  `step_status` int(11) NOT NULL,
  `tr_time` datetime NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_user_assign_groups` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  UNIQUE KEY `user_id` (`user_id`,`group_id`),
  KEY `group_id` (`group_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_user_assigned_groups` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL,
  `grp_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_user_courses` (
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY  (`user_id`,`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_user_parents` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `parent_id` (`parent_id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_user_permissions` (
  `role_id` int(11) NOT NULL default '0',
  `p_category` varchar(30) NOT NULL default '',
  `p_permission` varchar(30) NOT NULL default '',
  `p_value` int(11) default NULL,
  PRIMARY KEY  (`role_id`,`p_category`,`p_permission`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_usergroups` (
  `id` int(11) NOT NULL auto_increment,
  `owner_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `ug_name` varchar(100) NOT NULL,
  `ug_description` text NOT NULL,
  `ordering` int(11) NOT NULL default '0',
  `group_forum` tinyint(4) default '0',
  `group_chat` tinyint(4) default '0',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `publish_start_date` int(11) default '0',
  `publish_end_date` int(11) default '0',
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_users` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `lms_usertype_id` tinyint(4) default '0',
  `lms_block` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `user_id_2` (`user_id`),
  UNIQUE KEY `user_id` (`user_id`,`lms_usertype_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_users_in_global_groups` (
  `id` int(11) NOT NULL auto_increment,
  `group_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `role_id` int(11) default NULL,
  `subgroup1_id` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `group_id` (`group_id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_users_in_groups` (
  `course_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL default '2',
  `teacher_comment` text NOT NULL,
  `publish_start` tinyint(4) default '0',
  `start_date` date NOT NULL,
  `publish_end` tinyint(4) default '0',
  `end_date` date NOT NULL,
  `enrol_time` datetime NOT NULL,
  PRIMARY KEY  (`course_id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_usertypes` (
  `id` tinyint(4) NOT NULL auto_increment,
  `roletype_id` int(11) NOT NULL default '1',
  `lms_usertype` varchar(50) default NULL,
  `default_role` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_waiting_lists` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) default NULL,
  `user_id` int(11) default NULL,
  `ordering` int(11) default NULL,
  PRIMARY KEY  (`id`),
  KEY `course_id` (`course_id`,`user_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_user_roles_assignments` (
  `role_id` int(11) default NULL,
  `role_assign` int(11) default NULL,
  `value` int(11) NOT NULL
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_payments_history` (
  `id` int(11) NOT NULL auto_increment,
  `payment_id` int(11) NOT NULL default '0',
  `status` varchar(30) NOT NULL default '',
  `txn_id` varchar(30) NOT NULL default '',
  `date` date NOT NULL default '0000-00-00',
  `paypalsubscr` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `payment_id` (`payment_id`)
);
-- </query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_time_tracking` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time_spent` int(11) NOT NULL,
  `time_last_activity` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- <query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_time_tracking_resources` (
  `id` int(11) NOT NULL auto_increment,
  `course_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `resource_type` int(11) NOT NULL,
  `resource_id` int(11) default NULL,
  `item_id` int(11) NOT NULL,
  `time_spent` int(11) default NULL,
  `time_last_activity` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
);
-- <query>

-- <query>
CREATE TABLE IF NOT EXISTS `#__lms_topics_collapsible` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
);
-- <query>