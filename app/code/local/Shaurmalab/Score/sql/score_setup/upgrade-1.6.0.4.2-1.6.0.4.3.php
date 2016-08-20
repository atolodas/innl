<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `customer_group` ADD COLUMN store_ids TEXT NOT NULL;
ALTER TABLE `customer_group` ADD COLUMN permissions TEXT NOT NULL;
ALTER TABLE `customer_group` ADD COLUMN parent_id INT(10) NOT NULL default 0;
");


$installer->endSetup();