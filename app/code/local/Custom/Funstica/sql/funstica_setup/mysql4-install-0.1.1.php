<?php

$installer = $this;

$installer->startSetup();

$installer->run("ALTER TABLE city add column `lat` varchar(255) NOT NULL default ''");
$installer->run("ALTER TABLE city add column `long` varchar(255) NOT NULL default ''");

$installer->endSetup();