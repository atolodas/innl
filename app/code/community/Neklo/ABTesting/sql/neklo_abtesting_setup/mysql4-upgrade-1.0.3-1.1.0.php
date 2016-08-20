<?php
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("DROP TABLE IF EXISTS `". $this->getTable('neklo_abtesting/abtest') ."`");
$this->run("DROP TABLE IF EXISTS `". $this->getTable('neklo_abtesting/abpresentation') ."`");
$this->run("DROP TABLE IF EXISTS `". $this->getTable('neklo_abtesting/visitor') ."`");
$this->run("DROP TABLE IF EXISTS `". $this->getTable('neklo_abtesting/log') ."`");

$this->run("CREATE TABLE IF NOT EXISTS `". $this->getTable('neklo_abtesting/abtest') ."` (
    `abtest_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT '',
    `code` varchar(255) NOT NULL DEFAULT '',
    `status` tinyint(3) NOT NULL DEFAULT '0',
    `cookie_lifetime` tinyint(3) NOT NULL DEFAULT '0',
    `start_at` TIMESTAMP NULL,
    `end_at` TIMESTAMP NULL,
    `created_at` TIMESTAMP NULL,
    PRIMARY KEY (`abtest_id`),
    UNIQUE KEY `neklo_abtesting_unique` (`abtest_id`,`name`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='A/B-tests';");

$this->run("CREATE TABLE IF NOT EXISTS `". $this->getTable('neklo_abtesting/abpresentation') ."` (
    `presentation_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT '',
    `code` varchar(255) NOT NULL DEFAULT '',
    `status` tinyint(3) NOT NULL DEFAULT '0',
    `html_content` TEXT,
    `layout_update` TEXT,
    `created_at` TIMESTAMP NOT NULL,
    PRIMARY KEY (`presentation_id`),
    UNIQUE KEY `neklo_abpresentation_unique` (`presentation_id`,`name`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='A/B-presentations';");

$this->run("CREATE TABLE IF NOT EXISTS `". $this->getTable('neklo_abtesting/abtest_abpresentation') ."` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `abtest_id` int(10) unsigned NOT NULL,
    `abpresentation_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `neklo_abtesting_link_unique` (`id`,`abtest_id`,`abpresentation_id`),
    CONSTRAINT `FK_ABTEST_PRESENTATION_LNK_ABTEST_ID` FOREIGN KEY (`abtest_id`) REFERENCES `". $this->getTable('neklo_abtesting/abtest') ."` (`abtest_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_ABTEST_PRESENTATION_LNK_PRESENTATION_ID` FOREIGN KEY (`abpresentation_id`) REFERENCES `". $this->getTable('neklo_abtesting/abpresentation') ."` (`presentation_id`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='A/B-test, A/B-presentations linking';");

$this->run("CREATE TABLE IF NOT EXISTS `". $this->getTable('neklo_abtesting/abevent') ."` (
    `event_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL DEFAULT '',
    `code` varchar(255) NOT NULL DEFAULT '',
    PRIMARY KEY (`event_id`),
    UNIQUE KEY `neklo_abevent_unique` (`event_id`,`name`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='A/B-events';");

$this->run("INSERT INTO `". $this->getTable('neklo_abtesting/abevent') ."` VALUES (1, 'Inited', 'inited')");


$this->run("CREATE TABLE IF NOT EXISTS `". $this->getTable('neklo_abtesting/abtest_event') ."` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `abtest_id` int(10) unsigned NOT NULL,
    `event_id` int(10) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `neklo_abtesting_event_link_unique` (`id`,`abtest_id`,`event_id`),
    CONSTRAINT `FK_ABTEST_EVENT_LNK_ABTEST_ID` FOREIGN KEY (`abtest_id`) REFERENCES `". $this->getTable('neklo_abtesting/abtest') ."` (`abtest_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_ABTEST_EVENT_LNK_EVENT_ID` FOREIGN KEY (`event_id`) REFERENCES `". $this->getTable('neklo_abtesting/abevent') ."` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='A/B-test, A/B-events linking';");

$this->run("CREATE TABLE IF NOT EXISTS `". $this->getTable('neklo_abtesting/visitor') ."` (
    `visitor_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `customer_id` int(10) unsigned NOT NULL,
    `visits_count` int(10) unsigned NOT NULL,
    `utm_source` VARCHAR(255) NULL DEFAULT NULL,
    `utm_medium` VARCHAR(255) NULL DEFAULT NULL,
    `utm_campaign` VARCHAR(255) NULL DEFAULT NULL,
    `utm_content` VARCHAR(255) NULL DEFAULT NULL,
    `utm_term` VARCHAR(255) NULL DEFAULT NULL,
    `visitor_info` text NULL DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL,
    `updated_at` TIMESTAMP NULL,
    PRIMARY KEY (`visitor_id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Store Visitors';");

$this->run("TRUNCATE TABLE `". $this->getTable('neklo_abtesting/visitor') ."`");
$this->run("ALTER TABLE `". $this->getTable('neklo_abtesting/visitor') ."` AUTO_INCREMENT=1000");

$this->run("CREATE TABLE IF NOT EXISTS `". $this->getTable('neklo_abtesting/log') ."` (
    `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `visitor_id` int(10) unsigned NOT NULL,
    `abtest_abpresentation_id` int(10) unsigned NOT NULL,
    `abevent_id` int(10) unsigned NOT NULL,
    `created_at` TIMESTAMP NOT NULL,
    PRIMARY KEY (`log_id`),
    CONSTRAINT `FK_ABTEST_LOG_PRESENTATION_ID` FOREIGN KEY (`abtest_abpresentation_id`) REFERENCES `". $this->getTable('neklo_abtesting/abtest_abpresentation') ."` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_ABTEST_LOG_EVENT_ID` FOREIGN KEY (`abevent_id`) REFERENCES `". $this->getTable('neklo_abtesting/abevent') ."` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='A/B-testing Log';");

$this->endSetup();