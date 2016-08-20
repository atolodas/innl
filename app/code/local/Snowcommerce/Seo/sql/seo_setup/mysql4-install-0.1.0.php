<?php

$installer = $this;

$installer->startSetup();

$installer->run("

DROP TABLE IF EXISTS {$this->getTable('seo')};
CREATE TABLE {$this->getTable('seo')} (
  `seo_id` int(11) unsigned NOT NULL auto_increment,
  `store_id` text NOT NULL default '',
  `type` text NOT NULL default '',
  `category` text NOT NULL default '',
  `url`  text NOT NULL default '',
  `meta_title`  text NOT NULL default '',
  `meta_keyword`  text NOT NULL default '',
  `meta_description`  text NOT NULL default '',
  `head` text NOT NULL default '',
  `seo_tag` text NOT NULL default '',
  `robots` text NOT NULL default '',
  `canonical` text NOT NULL default '',
  `priority` text NOT NULL default '',
  `status` smallint(6) NOT NULL default '1',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`seo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
    ");

$installer->endSetup(); 
