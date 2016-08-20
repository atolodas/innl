<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('dcontent_templates')} ADD COLUMN `category` text NOT NULL");
$installer->run("ALTER TABLE {$this->getTable('dcontent_templates')} ADD COLUMN `additional_data` text NOT NULL");

$installer->endSetup(); 
