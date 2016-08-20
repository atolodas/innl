<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `score_eav_attribute` ADD COLUMN is_for_edit INT(1) NOT NULL default 1;
");


$installer->endSetup();