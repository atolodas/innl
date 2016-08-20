<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('dcontent_templates')} ADD COLUMN `store_id` text NOT NULL");

$installer->endSetup(); 
