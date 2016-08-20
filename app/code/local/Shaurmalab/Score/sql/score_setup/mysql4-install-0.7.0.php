<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Shaurmalab_Score_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

if (!$installer->tableExists($installer->getTable('score_category_entity'))) {

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('score_category_entity')};
CREATE TABLE {$this->getTable('score_category_entity')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_score_category_ENTITY_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_score_category_ENTITY_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category Entities';

-- DROP TABLE IF EXISTS {$this->getTable('score_category_entity_datetime')};
CREATE TABLE {$this->getTable('score_category_entity_datetime')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_DATETIME_ENTITY` (`entity_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_DATETIME_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DATETIME_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_category_entity_decimal')};
CREATE TABLE {$this->getTable('score_category_entity_decimal')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_DECIMAL_ENTITY` (`entity_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_DECIMAL_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DECIMAL_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_category_entity_int')};
CREATE TABLE {$this->getTable('score_category_entity_int')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_INT_ENTITY` (`entity_id`),
  KEY `FK_SCORE_CATEGORY_EMTITY_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_CATEGORY_EMTITY_INT_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_CATEGORY_EMTITY_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_EMTITY_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_EMTITY_INT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_category_entity_text')};
CREATE TABLE {$this->getTable('score_category_entity_text')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_TEXT_ENTITY` (`entity_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_TEXT_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_TEXT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_category_entity_varchar')};
CREATE TABLE {$this->getTable('score_category_entity_varchar')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` USING BTREE (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_VARCHAR_ENTITY` (`entity_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_VARCHAR_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_VARCHAR_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_category_oggetto')};
CREATE TABLE {$this->getTable('score_category_oggetto')} (
  `category_id` int(10) unsigned NOT NULL default '0',
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `position` int(10) unsigned NOT NULL default '0',
  KEY `CATALOG_CATEGORY_OGGETTO_CATEGORY` (`category_id`),
  KEY `CATALOG_CATEGORY_OGGETTO_OGGETTO` (`oggetto_id`),
  CONSTRAINT `CATALOG_CATEGORY_OGGETTO_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES {$this->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CATALOG_CATEGORY_OGGETTO_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_compare_item')};
CREATE TABLE {$this->getTable('score_compare_item')} (
  `score_compare_item_id` int(11) unsigned NOT NULL auto_increment,
  `visitor_id` int(11) unsigned NOT NULL default '0',
  `customer_id` int(11) unsigned default NULL,
  `oggetto_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY  (`score_compare_item_id`),
  KEY `FK_SCORE_COMPARE_ITEM_CUSTOMER` (`customer_id`),
  KEY `FK_SCORE_COMPARE_ITEM_OGGETTO` (`oggetto_id`),
  CONSTRAINT `FK_SCORE_COMPARE_ITEM_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES {$this->getTable('customer_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_COMPARE_ITEM_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_bundle_option')};
CREATE TABLE {$this->getTable('score_oggetto_bundle_option')} (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`option_id`),
  KEY `FK_score_oggetto_bundle_option` (`oggetto_id`),
  CONSTRAINT `FK_score_oggetto_bundle_option` FOREIGN KEY (`oggetto_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_bundle_option_link')};
CREATE TABLE {$this->getTable('score_oggetto_bundle_option_link')} (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `discount` decimal(10,4) unsigned default NULL,
  PRIMARY KEY  (`link_id`),
  KEY `FK_score_oggetto_bundle_option_link` (`option_id`),
  KEY `FK_score_oggetto_bundle_option_link_entity` (`oggetto_id`),
  CONSTRAINT `FK_score_oggetto_bundle_option_link` FOREIGN KEY (`option_id`) REFERENCES {$this->getTable('score_oggetto_bundle_option')} (`option_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_score_oggetto_bundle_option_link_entity` FOREIGN KEY (`oggetto_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_bundle_option_value')};
CREATE TABLE {$this->getTable('score_oggetto_bundle_option_value')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `option_id` int(10) unsigned NOT NULL default '0',
  `label` varchar(255) default NULL,
  `position` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_score_oggetto_bundle_option_label` (`option_id`),
  CONSTRAINT `FK_score_oggetto_bundle_option_label` FOREIGN KEY (`option_id`) REFERENCES {$this->getTable('score_oggetto_bundle_option')} (`option_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_entity')};
CREATE TABLE {$this->getTable('score_oggetto_entity')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `type_id` tinyint(3) unsigned NOT NULL default '1',
  `sku` varchar (64) default NULL,
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_ATTRIBUTE_SET_ID` (`attribute_set_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_STORE_ID` (`store_id`),
  KEY `sku` (`sku`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_ATTRIBUTE_SET_ID` FOREIGN KEY (`attribute_set_id`) REFERENCES {$this->getTable('eav_attribute_set')} (`attribute_set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES {$this->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Oggetto Entities';

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_entity_datetime')};
CREATE TABLE {$this->getTable('score_oggetto_entity_datetime')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DATETIME_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DATETIME_OGGETTO_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DATETIME_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DATETIME_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_entity_decimal')};
CREATE TABLE {$this->getTable('score_oggetto_entity_decimal')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DECIMAL_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DECIMAL_OGGETTO_ENTITY` (`entity_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DECIMAL_ATTRIBUTE` (`attribute_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DECIMAL_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DECIMAL_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_entity_gallery')};
CREATE TABLE {$this->getTable('score_oggetto_entity_gallery')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `position` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `IDX_BASE` USING BTREE (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_GALLERY_ENTITY` (`entity_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_GALLERY_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_GALLERY_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_GALLERY_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_GALLERY_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_GALLERY_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_entity_int')};
CREATE TABLE {$this->getTable('score_oggetto_entity_int')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_INT_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_INT_OGGETTO_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_INT_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_INT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_entity_text')};
CREATE TABLE {$this->getTable('score_oggetto_entity_text')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TEXT_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TEXT_OGGETTO_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TEXT_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TEXT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_entity_tier_price')};
CREATE TABLE {$this->getTable('score_oggetto_entity_tier_price')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `qty` smallint(5) unsigned NOT NULL default '1',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_OGGETTO_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_entity_varchar')};
CREATE TABLE {$this->getTable('score_oggetto_entity_varchar')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_VARCHAR_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_VARCHAR_OGGETTO_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$this->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_VARCHAR_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_VARCHAR_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_link')};
CREATE TABLE {$this->getTable('score_oggetto_link')} (
  `link_id` int(11) unsigned NOT NULL auto_increment,
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `linked_oggetto_id` int(10) unsigned NOT NULL default '0',
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `FK_LINK_OGGETTO` (`oggetto_id`),
  KEY `FK_LINKED_OGGETTO` (`linked_oggetto_id`),
  KEY `FK_OGGETTO_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_OGGETTO_LINK_LINKED_OGGETTO` FOREIGN KEY (`linked_oggetto_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_OGGETTO_LINK_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_OGGETTO_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES {$this->getTable('score_oggetto_link_type')} (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Related oggettos';

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_link_attribute')};
CREATE TABLE {$this->getTable('score_oggetto_link_attribute')} (
  `oggetto_link_attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  `oggetto_link_attribute_code` varchar(32) NOT NULL default '',
  `data_type` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`oggetto_link_attribute_id`),
  KEY `FK_ATTRIBUTE_OGGETTO_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_OGGETTO_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES {$this->getTable('score_oggetto_link_type')} (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attributes for oggetto link';

insert  into {$this->getTable('score_oggetto_link_attribute')}(`oggetto_link_attribute_id`,`link_type_id`,`oggetto_link_attribute_code`,`data_type`) values (1,2,'qty','decimal'),(2,1,'position','int'),(3,4,'position','int'),(4,5,'position','int'),(6,1,'qty','decimal'),(7,3,'position','int'),(8,3,'qty','decimal');

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_link_attribute_decimal')};
CREATE TABLE {$this->getTable('score_oggetto_link_attribute_decimal')} (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `oggetto_link_attribute_id` smallint(6) unsigned default NULL,
  `link_id` int(11) unsigned default NULL,
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_DECIMAL_OGGETTO_LINK_ATTRIBUTE` (`oggetto_link_attribute_id`),
  KEY `FK_DECIMAL_LINK` (`link_id`),
  CONSTRAINT `FK_DECIMAL_LINK` FOREIGN KEY (`link_id`) REFERENCES {$this->getTable('score_oggetto_link')} (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_DECIMAL_OGGETTO_LINK_ATTRIBUTE` FOREIGN KEY (`oggetto_link_attribute_id`) REFERENCES {$this->getTable('score_oggetto_link_attribute')} (`oggetto_link_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Decimal attributes values';

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_link_attribute_int')};
CREATE TABLE {$this->getTable('score_oggetto_link_attribute_int')} (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `oggetto_link_attribute_id` smallint(6) unsigned default NULL,
  `link_id` int(11) unsigned default NULL,
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_INT_OGGETTO_LINK_ATTRIBUTE` (`oggetto_link_attribute_id`),
  KEY `FK_INT_OGGETTO_LINK` (`link_id`),
  CONSTRAINT `FK_INT_OGGETTO_LINK` FOREIGN KEY (`link_id`) REFERENCES {$this->getTable('score_oggetto_link')} (`link_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_INT_OGGETTO_LINK_ATTRIBUTE` FOREIGN KEY (`oggetto_link_attribute_id`) REFERENCES {$this->getTable('score_oggetto_link_attribute')} (`oggetto_link_attribute_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_link_attribute_varchar')};
CREATE TABLE {$this->getTable('score_oggetto_link_attribute_varchar')} (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `oggetto_link_attribute_id` smallint(6) unsigned NOT NULL default '0',
  `link_id` int(11) unsigned default NULL,
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_VARCHAR_OGGETTO_LINK_ATTRIBUTE` (`oggetto_link_attribute_id`),
  KEY `FK_VARCHAR_LINK` (`link_id`),
  CONSTRAINT `FK_VARCHAR_LINK` FOREIGN KEY (`link_id`) REFERENCES {$this->getTable('score_oggetto_link')} (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_VARCHAR_OGGETTO_LINK_ATTRIBUTE` FOREIGN KEY (`oggetto_link_attribute_id`) REFERENCES {$this->getTable('score_oggetto_link_attribute')} (`oggetto_link_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Varchar attributes values';

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_link_type')};
CREATE TABLE {$this->getTable('score_oggetto_link_type')} (
  `link_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`link_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Types of oggetto link(Related, superoggetto, bundles)';

insert  into {$this->getTable('score_oggetto_link_type')}(`link_type_id`,`code`) values (1,'relation'),(2,'bundle'),(3,'super'),(4,'up_sell'),(5,'cross_sell');

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_status')};
CREATE TABLE {$this->getTable('score_oggetto_status')} (
  `status_id` tinyint(3) unsigned NOT NULL auto_increment,
  `status_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available oggetto statuses';

insert  into {$this->getTable('score_oggetto_status')}(`status_id`,`status_code`) values (1,'Enabled'),(2,'Disabled'),(3,'Out-of-stock');

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_store')};
CREATE TABLE {$this->getTable('score_oggetto_store')} (
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `IDX_PS_UNIQ` (`store_id`,`oggetto_id`),
  KEY `store_id` (`store_id`),
  KEY `FK_SCORE_PRDUCT_STORE_OGGETTO` (`oggetto_id`),
  CONSTRAINT `FK_SCORE_PRDUCT_STORE_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_PRDUCT_STORE_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_super_attribute')};
CREATE TABLE {$this->getTable('score_oggetto_super_attribute')} (
  `oggetto_super_attribute_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `position` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`oggetto_super_attribute_id`),
  KEY `FK_SUPER_OGGETTO_ATTRIBUTE_OGGETTO` (`oggetto_id`),
  CONSTRAINT `FK_SUPER_OGGETTO_ATTRIBUTE_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_super_attribute_label')};
CREATE TABLE {$this->getTable('score_oggetto_super_attribute_label')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_super_attribute_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `value` varchar(255) character set utf8 NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SUPER_OGGETTO_ATTRIBUTE_LABEL` (`oggetto_super_attribute_id`),
  CONSTRAINT `FK_SUPER_OGGETTO_ATTRIBUTE_LABEL` FOREIGN KEY (`oggetto_super_attribute_id`) REFERENCES {$this->getTable('score_oggetto_super_attribute')} (`oggetto_super_attribute_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_super_attribute_pricing')};
CREATE TABLE {$this->getTable('score_oggetto_super_attribute_pricing')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_super_attribute_id` int(10) unsigned NOT NULL default '0',
  `value_index` varchar(255) character set utf8 NOT NULL default '',
  `is_percent` tinyint(1) unsigned default '0',
  `pricing_value` decimal(10,4) default NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_SUPER_OGGETTO_ATTRIBUTE_PRICING` (`oggetto_super_attribute_id`),
  CONSTRAINT `FK_SUPER_OGGETTO_ATTRIBUTE_PRICING` FOREIGN KEY (`oggetto_super_attribute_id`) REFERENCES {$this->getTable('score_oggetto_super_attribute')} (`oggetto_super_attribute_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_super_link')};
CREATE TABLE {$this->getTable('score_oggetto_super_link')} (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `FK_SUPER_OGGETTO_LINK_PARENT` (`parent_id`),
  KEY `FK_score_oggetto_super_link` (`oggetto_id`),
  CONSTRAINT `FK_SUPER_OGGETTO_LINK_ENTITY` FOREIGN KEY (`oggetto_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_SUPER_OGGETTO_LINK_PARENT` FOREIGN KEY (`parent_id`) REFERENCES {$this->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_type')};
CREATE TABLE {$this->getTable('score_oggetto_type')} (
  `type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert  into {$this->getTable('score_oggetto_type')}(`type_id`,`code`) values (1,'Simple Oggetto'),(2,'bundle'),(3,'Configurable Oggetto'),(4,'Grouped Oggetto');

-- DROP TABLE IF EXISTS {$this->getTable('score_oggetto_visibility')};
CREATE TABLE {$this->getTable('score_oggetto_visibility')} (
  `visibility_id` tinyint(3) unsigned NOT NULL auto_increment,
  `visibility_code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`visibility_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Available oggetto visibility';

insert  into {$this->getTable('score_oggetto_visibility')}(`visibility_id`,`visibility_code`) values (1,'Nowhere'),(2,'Catalog'),(3,'Search'),(4,'Catalog, Search');

ALTER TABLE `{$this->getTable('score_category_entity')}` ADD `path` VARCHAR( 255 ) NOT NULL, ADD `position` INT NOT NULL;

ALTER TABLE `{$installer->getTable('score_category_entity')}` ADD `level` INT NOT NULL;
ALTER TABLE `{$installer->getTable('score_category_entity')}` ADD INDEX `IDX_LEVEL` ( `level` );


CREATE TABLE `{$installer->getTable('score_category_oggetto_index')}` (
    `category_id` int(10) unsigned NOT NULL default '0',
    `oggetto_id` int(10) unsigned NOT NULL default '0',
    `position` int(10) unsigned NOT NULL default '0',
    `is_parent` tinyint(1) unsigned NOT NULL default '0',
    UNIQUE KEY `UNQ_CATEGORY_OGGETTO` (`category_id`,`oggetto_id`),
    KEY `IDX_CATEGORY_POSITION` (`category_id`,`position`),
    CONSTRAINT `FK_SCORE_CATEGORY_OGGETTO_INDEX_OGGETTO_ENTITY` FOREIGN KEY (`oggetto_id`) REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SCORE_CATEGORY_OGGETTO_INDEX_CATEGORY_ENTITY` FOREIGN KEY (`category_id`) REFERENCES `{$installer->getTable('score_category_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `{$installer->getTable('score_oggetto_enabled_index')}` (
    `oggetto_id` int(10) unsigned NOT NULL default '0',
    `store_id` smallint(5) unsigned NOT NULL default '0',
    `visibility` smallint(5) unsigned NOT NULL default '0',
    UNIQUE KEY `UNQ_OGGETTO_STORE` (`oggetto_id`,`store_id`),
    KEY `IDX_OGGETTO_VISIBILITY_IN_STORE` (`oggetto_id`,`store_id`, `visibility`),
    CONSTRAINT `FK_SCORE_OGGETTO_ENABLED_INDEX_OGGETTO_ENTITY` FOREIGN KEY (`oggetto_id`) REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `FK_SCORE_OGGETTO_ENABLED_INDEX_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `{$this->getTable('score_category_entity')}` ADD `children_count` INT NOT NULL;

");

}

$installer->endSetup();

$installer->installEntities();

// Create Root Score Node
Mage::getModel('score/category')
    ->setId(1)
    ->setPath(1)
    ->setName('Root Catalog')
    ->setInitialSetupFlag(true)
    ->save();

$category = Mage::getModel('score/category');
/* @var $category Shaurmalab_Score_Model_Category */

$category->setStoreId(0)
    ->setName('Default Category')
    ->setDisplayMode('OGGETTOS')
    ->setAttributeSetId($category->getDefaultAttributeSetId())
    ->setIsActive(1)
    ->setPath('1/')
    ->setInitialSetupFlag(true)
    ->save();

$category->setStoreId(1)
    ->save();

$installer->setConfigData('score/category/root_id', $category->getId());
