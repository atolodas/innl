<?php
$installer = $this;

$installer->startSetup();

$installer->run("
	ALTER TABLE `eav_attribute_set` ADD COLUMN store_id INT(1) NOT NULL default 0;
");

$installer->endSetup();