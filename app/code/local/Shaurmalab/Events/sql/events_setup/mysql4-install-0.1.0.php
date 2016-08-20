<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('events')};
CREATE TABLE {$this->getTable('events')} (
  `events_id` int(11) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `event_type` varchar(255) NOT NULL default '',
  `oggetto_type` int(255) NOT NULL default 0,
  `changed_attribute` varchar(255) NOT NULL default '',
  `todo` varchar(255) NOT NULL default '',
  `related_oggettos` varchar(255) NOT NULL default '',
  `new_oggetto_type` varchar(255) NOT NULL default '',
  `attributes_values` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`events_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup();