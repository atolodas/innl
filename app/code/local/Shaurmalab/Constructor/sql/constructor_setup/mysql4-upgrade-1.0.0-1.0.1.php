<?php
$installer = $this;

$installer->startSetup();

$installer->run("
ALTER TABLE `core_store` ADD COLUMN created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
");

$installer->endSetup();