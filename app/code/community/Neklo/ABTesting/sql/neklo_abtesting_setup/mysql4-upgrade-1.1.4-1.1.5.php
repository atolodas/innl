<?php
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("ALTER TABLE `". $this->getTable('neklo_abtesting/log') ."` 
			ADD COLUMN visitor_info text NULL DEFAULT NULL");

$this->endSetup();