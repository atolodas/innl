<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('popup')};
CREATE TABLE {$this->getTable('popup')} (
  `popup_id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `text_id` varchar(255) NOT NULL default '',
  `action` varchar(20) NOT NULL default '',
  `type` int(1) NOT NULL,
  `url` text NOT NULL,
  `url2` text NOT NULL,
  `block` varchar(255) NOT NULL default '',
  `preload` int(1),
  `style` text NOT NULL,
  `status` int(1) NOT NULL,
  
  PRIMARY KEY (`popup_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 