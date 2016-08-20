<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('dcontent')};
CREATE TABLE {$this->getTable('dcontent')} (
  `dcontent_id` int(11) unsigned NOT NULL auto_increment,
  `title` text NOT NULL default '',
  `products` text NOT NULL,
  `products_per_line` smallint(6) NOT NULL default '0',
  `status` smallint(6) NOT NULL default '1',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`dcontent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 