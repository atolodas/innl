<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('dcontent')} ADD COLUMN `image` text NOT NULL");

$installer->endSetup(); 
