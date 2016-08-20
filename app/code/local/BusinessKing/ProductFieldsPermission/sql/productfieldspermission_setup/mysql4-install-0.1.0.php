<?php
/**
 * @category   BusinessKing
 * @package    BusinessKing_ProductFieldsPermission
 */

$installer = $this;
/* @var $installer Mage_Eav_Model_Entity_Setup */

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS `{$this->getTable('role_attributes')}`;

CREATE TABLE `{$this->getTable('role_attributes')}` (
   `role_id` INTEGER UNSIGNED NOT NULL,
   `attribute_id` INTEGER UNSIGNED NOT NULL DEFAULT 0,
   `tab_name` VARCHAR(45) NOT NULL,
   PRIMARY KEY (`role_id`,`attribute_id`,`tab_name`),
   KEY `ROLE` (`role_id`),
   CONSTRAINT `ROLE` FOREIGN KEY (`role_id`) REFERENCES {$this->getTable('admin_role')} (`role_id`) ON DELETE CASCADE ON UPDATE CASCADE      
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();
