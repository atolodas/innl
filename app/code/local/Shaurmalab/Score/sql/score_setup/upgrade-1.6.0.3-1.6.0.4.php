<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `score_eav_attribute` ADD COLUMN is_for_logged_in INT(1) NOT NULL default 0;
");


$installer->endSetup();