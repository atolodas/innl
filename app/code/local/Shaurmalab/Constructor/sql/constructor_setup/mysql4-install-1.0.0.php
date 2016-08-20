<?php
$installer = $this;

$installer->startSetup();




$installer->run("
ALTER TABLE `core_store` ADD COLUMN owner VARCHAR(50) NOT NULL default '';
ALTER TABLE `core_store` ADD COLUMN is_public INT(1) NOT NULL default 0;
ALTER TABLE `core_store` ADD COLUMN share_to TEXT NOT NULL default '';
");



$installer->endSetup();