<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('dcontent_templates')} ADD COLUMN `col_left` text NOT NULL");

$installer->run("ALTER TABLE {$this->getTable('dcontent_templates')} ADD COLUMN `col_right` text NOT NULL");

$installer->endSetup(); 
