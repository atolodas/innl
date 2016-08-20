<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('dcontent')} ADD COLUMN `block_type` varchar(255) NOT NULL default 'simple'");

$installer->endSetup(); 