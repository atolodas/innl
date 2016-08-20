<?php

$installer = $this;

$installer->startSetup();

$installer->run("


ALTER TABLE {$this->getTable('cacherecords')} ADD COLUMN `title` text NOT NULL default ''");


$installer->endSetup(); 