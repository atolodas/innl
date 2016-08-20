<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('cacherecords')};
CREATE TABLE {$this->getTable('cacherecords')} (
  `cacherecords_id` int(11) unsigned NOT NULL auto_increment,
  `url` text NOT NULL default '',
  `mkeys` text NOT NULL default '',
  `md5key` text NOT NULL default '',
  `created_time` datetime NULL,
 
  PRIMARY KEY (`cacherecords_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");


$installer->endSetup(); 