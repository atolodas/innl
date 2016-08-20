<?php
/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$this->run("ALTER TABLE `". $this->getTable('neklo_abtesting/visitor') ."` 
			ADD COLUMN uri VARCHAR(255) NULL DEFAULT NULL,
            ADD COLUMN user_agent text NULL DEFAULT NULL,
          	ADD COLUMN is_banned tinyint(1) NOT NULL DEFAULT '0'");

$this->run("update visitors set 
			 user_agent = SUBSTRING_INDEX(SUBSTRING_INDEX(visitor_info,' | ',3),' | ',-1), 
			 visitor_ip = SUBSTRING_INDEX(SUBSTRING_INDEX(visitor_info,' | ',2),' | ',-1), 
			 uri = SUBSTRING_INDEX(visitor_info,' | ',1)");

$this->endSetup();