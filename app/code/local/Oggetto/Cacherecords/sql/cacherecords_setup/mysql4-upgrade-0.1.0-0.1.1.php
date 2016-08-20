<?php

$installer = $this;

$installer->startSetup();

$installer->run("


ALTER TABLE {$this->getTable('cacherecords')} ADD COLUMN `file_exist` text NOT NULL default ''");


$installer->endSetup(); 