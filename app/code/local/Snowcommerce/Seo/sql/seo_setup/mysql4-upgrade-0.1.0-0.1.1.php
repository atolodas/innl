<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE {$this->getTable('seo')} ADD COLUMN `oggetto_type` text NOT NULL");


$installer->endSetup(); 
