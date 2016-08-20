<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `eav_attribute_set` ADD COLUMN page INT(1) NOT NULL default 0;
");



$installer->endSetup();