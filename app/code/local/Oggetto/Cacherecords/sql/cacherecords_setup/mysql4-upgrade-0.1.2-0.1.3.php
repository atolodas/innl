<?php

$installer = $this;

$installer->startSetup();

$installer->run("


ALTER TABLE {$this->getTable('cacherecords')} ADD COLUMN `content` text NOT NULL default ''");


$installer->endSetup(); 