<?php
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("UPDATE `". $this->getTable('neklo_abtesting/visitor') ."` 
			SET visits_count = 1 
			WHERE visits_count = 0;");

$this->endSetup();