<?php

$installer = $this;

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS {$this->getTable('dcontent_templates')};
CREATE TABLE {$this->getTable('dcontent_templates')} (
  `dcontent_id` int(11) unsigned NOT NULL auto_increment,
  `header` text NOT NULL default '',
  `product` text NOT NULL default '',
  `separator` text NOT NULL default '',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`dcontent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup(); 