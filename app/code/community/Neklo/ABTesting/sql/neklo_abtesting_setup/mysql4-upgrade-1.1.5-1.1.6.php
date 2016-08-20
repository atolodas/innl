<?php
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("ALTER TABLE `". $this->getTable('neklo_abtesting/visitor') ."` 
			ADD COLUMN visitor_ip text NULL DEFAULT NULL");

$this->endSetup();