<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('dcontent_templates')} ADD COLUMN `before_products` text NOT NULL");
$installer->run("ALTER TABLE {$this->getTable('dcontent_templates')} ADD COLUMN `after_products` text NOT NULL");

$installer->endSetup(); 