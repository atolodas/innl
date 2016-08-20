<?php
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("UPDATE `". $this->getTable('neklo_abtesting/abevent') ."` 
			SET code = 'controller_action_predispatch_catalog_product_view' 
			WHERE code = 'catalog_controller_product_view';
			");


$this->endSetup();