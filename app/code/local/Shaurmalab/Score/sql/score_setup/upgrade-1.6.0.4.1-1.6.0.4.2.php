<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `eav_attribute_set` ADD COLUMN core_permissions INT(1) NOT NULL default 0;
ALTER TABLE `eav_attribute_set` ADD COLUMN assign_customers INT(1) NOT NULL default 0;
");



$installer->endSetup();