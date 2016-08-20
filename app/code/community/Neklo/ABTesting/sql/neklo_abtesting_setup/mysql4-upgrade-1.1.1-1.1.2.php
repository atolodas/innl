<?php
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("
ALTER TABLE `". $this->getTable('neklo_abtesting/abtest_abpresentation') ."` 
				ADD COLUMN chance tinyint(3) NOT NULL DEFAULT '50'
");


$this->endSetup();