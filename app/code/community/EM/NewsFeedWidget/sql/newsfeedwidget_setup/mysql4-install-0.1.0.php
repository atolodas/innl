<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('newsfeedwidget')};
CREATE TABLE IF NOT EXISTS {$this->getTable('newsfeedwidget')} (
  `feed_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `link` text NOT NULL,
  `expired` datetime NOT NULL,
  PRIMARY KEY (`feed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS {$this->getTable('newsfeedwidget_item')};
CREATE TABLE IF NOT EXISTS {$this->getTable('newsfeedwidget_item')} (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `feed_id` int(10) unsigned NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `title` text,
  `link` text,
  `description` text,
  `image` text,
  PRIMARY KEY (`item_id`),
  KEY `feed_id` (`feed_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE {$this->getTable('newsfeedwidget_item')}
  ADD CONSTRAINT `newsfeedwidget_item_ibfk_1` FOREIGN KEY (`feed_id`) REFERENCES {$this->getTable('newsfeedwidget')} (`feed_id`) ON DELETE CASCADE ON UPDATE CASCADE;
    ");

$installer->endSetup(); 
