<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('dcontent_templates')} ADD COLUMN `type` text NOT NULL");

$installer->run("ALTER TABLE {$this->getTable('dcontent_templates')} ADD COLUMN `kind` text NOT NULL");
$installer->endSetup(); 
