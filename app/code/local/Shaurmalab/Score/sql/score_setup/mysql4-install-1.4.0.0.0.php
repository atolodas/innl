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

/* @var $installer Shaurmalab_Score_Model_Resource_Eav_Mysql4_Setup */
$installer = $this;

$installer->startSetup();

if (!$installer->tableExists($installer->getTable('score_category_entity'))) {

$installer->run("

-- DROP TABLE IF EXISTS {$installer->getTable('score_category_entity')};
CREATE TABLE {$installer->getTable('score_category_entity')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `path` varchar(255) NOT NULL,
  `position` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `children_count` int(11) NOT NULL,
  PRIMARY KEY  (`entity_id`),
  KEY `IDX_LEVEL` (`level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Category Entities';

-- DROP TABLE IF EXISTS {$installer->getTable('score_category_entity_datetime')};
CREATE TABLE {$installer->getTable('score_category_entity_datetime')} (
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
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DATETIME_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_category_entity_decimal')};
CREATE TABLE {$installer->getTable('score_category_entity_decimal')} (
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
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_DECIMAL_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_category_entity_int')};
CREATE TABLE {$installer->getTable('score_category_entity_int')} (
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
  CONSTRAINT `FK_SCORE_CATEGORY_EMTITY_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_EMTITY_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_EMTITY_INT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_category_entity_text')};
CREATE TABLE {$installer->getTable('score_category_entity_text')} (
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
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_TEXT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_category_entity_varchar')};
CREATE TABLE {$installer->getTable('score_category_entity_varchar')} (
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
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_ENTITY_VARCHAR_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_category_oggetto')};
CREATE TABLE {$installer->getTable('score_category_oggetto')} (
  `category_id` int(10) unsigned NOT NULL default '0',
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `position` int(10) unsigned NOT NULL default '0',
  UNIQUE KEY `UNQ_CATEGORY_OGGETTO` (`category_id`,`oggetto_id`),
  KEY `CATALOG_CATEGORY_OGGETTO_CATEGORY` (`category_id`),
  KEY `CATALOG_CATEGORY_OGGETTO_OGGETTO` (`oggetto_id`),
  CONSTRAINT `CATALOG_CATEGORY_OGGETTO_CATEGORY` FOREIGN KEY (`category_id`) REFERENCES {$installer->getTable('score_category_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CATALOG_CATEGORY_OGGETTO_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_compare_item')};
CREATE TABLE {$installer->getTable('score_compare_item')} (
  `score_compare_item_id` int(11) unsigned NOT NULL auto_increment,
  `visitor_id` int(11) unsigned NOT NULL default '0',
  `customer_id` int(11) unsigned default NULL,
  `oggetto_id` int(11) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned DEFAULT NULL,
  PRIMARY KEY  (`score_compare_item_id`),
  KEY `FK_SCORE_COMPARE_ITEM_CUSTOMER` (`customer_id`),
  KEY `FK_SCORE_COMPARE_ITEM_OGGETTO` (`oggetto_id`),
  KEY `IDX_VISITOR_OGGETTOS` (`visitor_id`,`oggetto_id`),
  KEY `IDX_CUSTOMER_OGGETTOS` (`customer_id`,`oggetto_id`),
  KEY `FK_SCORE_COMPARE_ITEM_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_COMPARE_ITEM_CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES {$installer->getTable('customer_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_COMPARE_ITEM_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_COMPARE_ITEM_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_bundle_option')};
CREATE TABLE {$installer->getTable('score_oggetto_bundle_option')} (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`option_id`),
  KEY `FK_score_oggetto_bundle_option` (`oggetto_id`),
  CONSTRAINT `FK_score_oggetto_bundle_option` FOREIGN KEY (`oggetto_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_bundle_option_link')};
CREATE TABLE {$installer->getTable('score_oggetto_bundle_option_link')} (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `discount` decimal(10,4) unsigned default NULL,
  PRIMARY KEY  (`link_id`),
  KEY `FK_score_oggetto_bundle_option_link` (`option_id`),
  KEY `FK_score_oggetto_bundle_option_link_entity` (`oggetto_id`),
  CONSTRAINT `FK_score_oggetto_bundle_option_link` FOREIGN KEY (`option_id`) REFERENCES {$installer->getTable('score_oggetto_bundle_option')} (`option_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_score_oggetto_bundle_option_link_entity` FOREIGN KEY (`oggetto_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_bundle_option_value')};
CREATE TABLE {$installer->getTable('score_oggetto_bundle_option_value')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `option_id` int(10) unsigned NOT NULL default '0',
  `label` varchar(255) default NULL,
  `position` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_score_oggetto_bundle_option_label` (`option_id`),
  CONSTRAINT `FK_score_oggetto_bundle_option_label` FOREIGN KEY (`option_id`) REFERENCES {$installer->getTable('score_oggetto_bundle_option')} (`option_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_entity')};
CREATE TABLE {$installer->getTable('score_oggetto_entity')} (
  `entity_id` int(10) unsigned NOT NULL auto_increment,
  `entity_type_id` smallint(8) unsigned NOT NULL default '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL default '0',
  `type_id` varchar(32) NOT NULL DEFAULT 'simple',
  `sku` varchar (64) default NULL,
  `has_options` smallint(1) NOT NULL DEFAULT '0',
  `required_options` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`entity_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_ATTRIBUTE_SET_ID` (`attribute_set_id`),
  KEY `sku` (`sku`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_ATTRIBUTE_SET_ID` FOREIGN KEY (`attribute_set_id`) REFERENCES {$installer->getTable('eav_attribute_set')} (`attribute_set_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES {$installer->getTable('eav_entity_type')} (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Oggetto Entities';

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_entity_datetime')};
CREATE TABLE {$installer->getTable('score_oggetto_entity_datetime')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DATETIME_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DATETIME_OGGETTO_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DATETIME_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DATETIME_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_entity_decimal')};
CREATE TABLE {$installer->getTable('score_oggetto_entity_decimal')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DECIMAL_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DECIMAL_OGGETTO_ENTITY` (`entity_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_DECIMAL_ATTRIBUTE` (`attribute_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DECIMAL_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_DECIMAL_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_entity_gallery')};
CREATE TABLE {$installer->getTable('score_oggetto_entity_gallery')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` smallint(5) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `position` int(11) NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_BASE` (`entity_type_id`,`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_ATTRIBUTE_GALLERY_ENTITY` (`entity_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_GALLERY_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_CATEGORY_ENTITY_GALLERY_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_GALLERY_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_GALLERY_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_GALLERY_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_entity_int')};
CREATE TABLE {$installer->getTable('score_oggetto_entity_int')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_INT_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_INT_OGGETTO_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_INT_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_INT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_entity_text')};
CREATE TABLE {$installer->getTable('score_oggetto_entity_text')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` text NOT NULL,
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TEXT_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TEXT_OGGETTO_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TEXT_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TEXT_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_entity_tier_price')};
CREATE TABLE {$installer->getTable('score_oggetto_entity_tier_price')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_id` int(10) unsigned NOT NULL default '0',
  `all_groups` tinyint (1)unsigned NOT NULL DEFAULT '1',
  `customer_group_id` smallint(5) unsigned NOT NULL default '0',
  `qty` decimal(12,4) NOT NULL default 1,
  `value` decimal(12,4) NOT NULL default '0.0000',
  `website_id` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_OGGETTO_ENTITY` (`entity_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_GROUP` (`customer_group_id`),
  KEY `FK_SCORE_OGGETTO_TIER_WEBSITE` (`website_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_GROUP` FOREIGN KEY (`customer_group_id`) REFERENCES {$installer->getTable('customer_group')} (`customer_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_TIER_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_entity_varchar')};
CREATE TABLE {$installer->getTable('score_oggetto_entity_varchar')} (
  `value_id` int(11) NOT NULL auto_increment,
  `entity_type_id` mediumint(8) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `IDX_ATTRIBUTE_VALUE` (`entity_id`,`attribute_id`,`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_VARCHAR_STORE` (`store_id`),
  KEY `FK_SCORE_OGGETTO_ENTITY_VARCHAR_OGGETTO_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES {$installer->getTable('eav_attribute')} (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_VARCHAR_OGGETTO_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENTITY_VARCHAR_STORE` FOREIGN KEY (`store_id`) REFERENCES {$installer->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_link')};
CREATE TABLE {$installer->getTable('score_oggetto_link')} (
  `link_id` int(11) unsigned NOT NULL auto_increment,
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `linked_oggetto_id` int(10) unsigned NOT NULL default '0',
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `FK_LINK_OGGETTO` (`oggetto_id`),
  KEY `FK_LINKED_OGGETTO` (`linked_oggetto_id`),
  KEY `FK_OGGETTO_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_OGGETTO_LINK_LINKED_OGGETTO` FOREIGN KEY (`linked_oggetto_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_OGGETTO_LINK_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_OGGETTO_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES {$installer->getTable('score_oggetto_link_type')} (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Related oggettos';

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_link_attribute')};
CREATE TABLE {$installer->getTable('score_oggetto_link_attribute')} (
  `oggetto_link_attribute_id` smallint(6) unsigned NOT NULL auto_increment,
  `link_type_id` tinyint(3) unsigned NOT NULL default '0',
  `oggetto_link_attribute_code` varchar(32) NOT NULL default '',
  `data_type` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`oggetto_link_attribute_id`),
  KEY `FK_ATTRIBUTE_OGGETTO_LINK_TYPE` (`link_type_id`),
  CONSTRAINT `FK_ATTRIBUTE_OGGETTO_LINK_TYPE` FOREIGN KEY (`link_type_id`) REFERENCES {$installer->getTable('score_oggetto_link_type')} (`link_type_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Attributes for oggetto link';

insert  into {$installer->getTable('score_oggetto_link_attribute')}(`oggetto_link_attribute_id`,`link_type_id`,`oggetto_link_attribute_code`,`data_type`) values (1,2,'qty','decimal'),(2,1,'position','int'),(3,4,'position','int'),(4,5,'position','int'),(6,1,'qty','decimal'),(7,3,'position','int'),(8,3,'qty','decimal');

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_link_attribute_decimal')};
CREATE TABLE {$installer->getTable('score_oggetto_link_attribute_decimal')} (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `oggetto_link_attribute_id` smallint(6) unsigned default NULL,
  `link_id` int(11) unsigned default NULL,
  `value` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`value_id`),
  KEY `FK_DECIMAL_OGGETTO_LINK_ATTRIBUTE` (`oggetto_link_attribute_id`),
  KEY `FK_DECIMAL_LINK` (`link_id`),
  CONSTRAINT `FK_DECIMAL_LINK` FOREIGN KEY (`link_id`) REFERENCES {$installer->getTable('score_oggetto_link')} (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_DECIMAL_OGGETTO_LINK_ATTRIBUTE` FOREIGN KEY (`oggetto_link_attribute_id`) REFERENCES {$installer->getTable('score_oggetto_link_attribute')} (`oggetto_link_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Decimal attributes values';

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_link_attribute_int')};
CREATE TABLE {$installer->getTable('score_oggetto_link_attribute_int')} (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `oggetto_link_attribute_id` smallint(6) unsigned default NULL,
  `link_id` int(11) unsigned default NULL,
  `value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_INT_OGGETTO_LINK_ATTRIBUTE` (`oggetto_link_attribute_id`),
  KEY `FK_INT_OGGETTO_LINK` (`link_id`),
  CONSTRAINT `FK_INT_OGGETTO_LINK` FOREIGN KEY (`link_id`) REFERENCES {$installer->getTable('score_oggetto_link')} (`link_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_INT_OGGETTO_LINK_ATTRIBUTE` FOREIGN KEY (`oggetto_link_attribute_id`) REFERENCES {$installer->getTable('score_oggetto_link_attribute')} (`oggetto_link_attribute_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_link_attribute_varchar')};
CREATE TABLE {$installer->getTable('score_oggetto_link_attribute_varchar')} (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `oggetto_link_attribute_id` smallint(6) unsigned NOT NULL default '0',
  `link_id` int(11) unsigned default NULL,
  `value` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  KEY `FK_VARCHAR_OGGETTO_LINK_ATTRIBUTE` (`oggetto_link_attribute_id`),
  KEY `FK_VARCHAR_LINK` (`link_id`),
  CONSTRAINT `FK_VARCHAR_LINK` FOREIGN KEY (`link_id`) REFERENCES {$installer->getTable('score_oggetto_link')} (`link_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_VARCHAR_OGGETTO_LINK_ATTRIBUTE` FOREIGN KEY (`oggetto_link_attribute_id`) REFERENCES {$installer->getTable('score_oggetto_link_attribute')} (`oggetto_link_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Varchar attributes values';

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_link_type')};
CREATE TABLE {$installer->getTable('score_oggetto_link_type')} (
  `link_type_id` tinyint(3) unsigned NOT NULL auto_increment,
  `code` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`link_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Types of oggetto link(Related, superoggetto, bundles)';

insert  into {$installer->getTable('score_oggetto_link_type')}(`link_type_id`,`code`) values (1,'relation'),(2,'bundle'),(3,'super'),(4,'up_sell'),(5,'cross_sell');

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_super_attribute')};
CREATE TABLE {$installer->getTable('score_oggetto_super_attribute')} (
  `oggetto_super_attribute_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `position` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`oggetto_super_attribute_id`),
  KEY `FK_SUPER_OGGETTO_ATTRIBUTE_OGGETTO` (`oggetto_id`),
  CONSTRAINT `FK_SUPER_OGGETTO_ATTRIBUTE_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES {$installer->getTable('score_oggetto_entity')} (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_super_attribute_label')};
CREATE TABLE {$installer->getTable('score_oggetto_super_attribute_label')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_super_attribute_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `use_default` tinyint(1) unsigned DEFAULT '0',
  `value` varchar(255) character set utf8 NOT NULL default '',
  PRIMARY KEY  (`value_id`),
  UNIQUE KEY `UNQ_ATTRIBUTE_STORE` (`oggetto_super_attribute_id`,`store_id`),
  KEY `FK_SUPER_OGGETTO_ATTRIBUTE_LABEL` (`oggetto_super_attribute_id`),
  KEY `FK_SCORE_OGGETTO_SUPER_ATTRIBUTE_LABEL_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_SUPER_ATTRIBUTE_LABEL_ATTRIBUTE` FOREIGN KEY (`oggetto_super_attribute_id`) REFERENCES `{$installer->getTable('score_oggetto_super_attribute')}` (`oggetto_super_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_SUPER_ATTRIBUTE_LABEL_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_super_attribute_pricing')};
CREATE TABLE {$installer->getTable('score_oggetto_super_attribute_pricing')} (
  `value_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_super_attribute_id` int(10) unsigned NOT NULL default '0',
  `value_index` varchar(255) character set utf8 NOT NULL default '',
  `is_percent` tinyint(1) unsigned default '0',
  `pricing_value` decimal(10,4) default NULL,
  `website_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY  (`value_id`),
  KEY `FK_SUPER_OGGETTO_ATTRIBUTE_PRICING` (`oggetto_super_attribute_id`),
  KEY `FK_SCORE_OGGETTO_SUPER_PRICE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_SUPER_PRICE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SUPER_OGGETTO_ATTRIBUTE_PRICING` FOREIGN KEY (`oggetto_super_attribute_id`) REFERENCES `{$installer->getTable('score_oggetto_super_attribute')}` (`oggetto_super_attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_super_link')};
CREATE TABLE {$installer->getTable('score_oggetto_super_link')} (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `parent_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`link_id`),
  KEY `FK_SUPER_OGGETTO_LINK_PARENT` (`parent_id`),
  KEY `FK_score_oggetto_super_link` (`oggetto_id`),
  CONSTRAINT `FK_SUPER_OGGETTO_LINK_ENTITY` FOREIGN KEY (`oggetto_id`) REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SUPER_OGGETTO_LINK_PARENT` FOREIGN KEY (`parent_id`) REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_category_oggetto_index')};
CREATE TABLE `{$installer->getTable('score_category_oggetto_index')}` (
  `category_id` int(10) unsigned NOT NULL default '0',
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `position` int(10) unsigned NOT NULL default '0',
  `is_parent` tinyint(1) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `visibility` tinyint(3) unsigned NOT NULL,
  UNIQUE KEY `UNQ_CATEGORY_OGGETTO` (`category_id`,`oggetto_id`,`is_parent`,`store_id`),
  KEY `FK_SCORE_CATEGORY_OGGETTO_INDEX_CATEGORY_ENTITY` (`category_id`),
  KEY `IDX_JOIN` (`oggetto_id`,`store_id`,`category_id`,`visibility`),
  KEY `IDX_BASE` (`store_id`,`category_id`,`visibility`,`is_parent`,`position`),
  CONSTRAINT `FK_SCORE_CATEGORY_OGGETTO_INDEX_OGGETTO_ENTITY` FOREIGN KEY (`oggetto_id`) REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_CATEGORY_OGGETTO_INDEX_CATEGORY_ENTITY` FOREIGN KEY (`category_id`) REFERENCES `{$installer->getTable('score_category_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CATEGORY_OGGETTO_INDEX_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_enabled_index')};
CREATE TABLE `{$installer->getTable('score_oggetto_enabled_index')}` (
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `visibility` smallint(5) unsigned NOT NULL default '0',
  UNIQUE KEY `UNQ_OGGETTO_STORE` (`oggetto_id`,`store_id`),
  KEY `IDX_OGGETTO_VISIBILITY_IN_STORE` (`oggetto_id`,`store_id`, `visibility`),
  CONSTRAINT `FK_SCORE_OGGETTO_ENABLED_INDEX_OGGETTO_ENTITY` FOREIGN KEY (`oggetto_id`) REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_ENABLED_INDEX_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

update {$installer->getTable('eav_entity_attribute')} set `sort_order`=10 where `attribute_id`=(select `attribute_id` from {$installer->getTable('eav_attribute')} where `attribute_code`='tier_price');

-- DROP TABLE IF EXISTS {$installer->getTable('score_oggetto_website')};
CREATE TABLE {$installer->getTable('score_oggetto_website')} (
  `oggetto_id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `website_id` SMALLINT(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`oggetto_id`, `website_id`),
  KEY `FK_SCORE_OGGETTO_WEBSITE_WEBSITE` (`website_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_WEBSITE_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$installer->getTable('core/website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_WEBSITE_OGGETTO_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT = FIXED;

-- DROP TABLE IF EXISTS `{$installer->getTable('score_oggetto_entity_media_gallery')}`;
CREATE TABLE `{$installer->getTable('score_oggetto_entity_media_gallery')}` (
  `value_id` int(11) unsigned NOT NULL auto_increment,
  `attribute_id` smallint(5) unsigned NOT NULL default '0',
  `entity_id` int(10) unsigned NOT NULL default '0',
  `value` varchar(255) default NULL,
  PRIMARY KEY  (`value_id`),
  KEY `FK_SCORE_OGGETTO_MEDIA_GALLERY_ATTRIBUTE` (`attribute_id`),
  KEY `FK_SCORE_OGGETTO_MEDIA_GALLERY_ENTITY` (`entity_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_MEDIA_GALLERY_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_MEDIA_GALLERY_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Catalog oggetto media gallery';

-- DROP TABLE IF EXISTS `{$installer->getTable('score_oggetto_entity_media_gallery_value')}`;
CREATE TABLE `{$installer->getTable('score_oggetto_entity_media_gallery_value')}` (
  `value_id` int(11) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `label` varchar(255) default NULL,
  `position` int(11) unsigned default NULL,
  `disabled` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`value_id`,`store_id`),
  KEY `FK_SCORE_OGGETTO_MEDIA_GALLERY_VALUE_STORE` (`store_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_MEDIA_GALLERY_VALUE_GALLERY` FOREIGN KEY (`value_id`) REFERENCES `{$installer->getTable('score_oggetto_entity_media_gallery')}` (`value_id`) ON DELETE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_MEDIA_GALLERY_VALUE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Catalog oggetto media gallery values';

");

$installer->getConnection()->dropColumn($installer->getTable('eav_attribute'), 'use_in_super_oggetto');

$installer->getConnection()->addColumn($installer->getTable('core_url_rewrite'), 'category_id', 'int unsigned NULL AFTER `store_id`');
$installer->getConnection()->addColumn($installer->getTable('core_url_rewrite'), 'oggetto_id', 'int unsigned NULL AFTER `category_id`');
$installer->getConnection()->addConstraint('FK_CORE_URL_REWRITE_CATEGORY', $installer->getTable('core_url_rewrite'), 'category_id', $installer->getTable('score_category_entity'), 'entity_id');
$installer->getConnection()->addConstraint('FK_CORE_URL_REWRITE_OGGETTO', $installer->getTable('core_url_rewrite'), 'oggetto_id', $installer->getTable('score_oggetto_entity'), 'entity_id');

$installer->run("
UPDATE `{$installer->getTable('eav_attribute')}` SET `position` = 1 WHERE `position` = 0 AND `attribute_code` != 'price';

-- DROP TABLE IF EXISTS `{$installer->getTable('score/oggetto_option')}`;
CREATE TABLE `{$installer->getTable('score/oggetto_option')}` (
  `option_id` int(10) unsigned NOT NULL auto_increment,
  `oggetto_id` int(10) unsigned NOT NULL default '0',
  `type` varchar(50) NOT NULL default '',
  `is_require` tinyint(1) NOT NULL default '1',
  `sku` varchar(64) NOT NULL default '',
  `max_characters` int(10) unsigned default NULL,
  `file_extension` varchar(50) default NULL,
  `image_size_x` smallint(5) unsigned NOT NULL,
  `image_size_y` smallint(5) unsigned NOT NULL,
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`option_id`),
  KEY `CATALOG_OGGETTO_OPTION_OGGETTO` (`oggetto_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES `{$installer->getTable('score/oggetto')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB default CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('score/oggetto_option_price')}`;
CREATE TABLE `{$installer->getTable('score/oggetto_option_price')}` (
  `option_price_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `price` decimal(12,4) NOT NULL default '0.00',
  `price_type` enum('fixed', 'percent') NOT NULL default 'fixed',
  PRIMARY KEY (`option_price_id`),
  KEY `CATALOG_OGGETTO_OPTION_PRICE_OPTION` (`option_id`),
  KEY `CATALOG_OGGETTO_OPTION_TITLE_STORE` (`store_id`),
  KEY `IDX_CATALOG_OGGETTO_OPTION_PRICE_SI_OI` (`store_id`,`option_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_PRICE_OPTION` FOREIGN KEY (`option_id`) REFERENCES `{$installer->getTable('score/oggetto_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_PRICE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB default CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('score/oggetto_option_title')}`;
CREATE TABLE `{$installer->getTable('score/oggetto_option_title')}` (
  `option_title_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `title` VARCHAR(255) NOT NULL default '',
  PRIMARY KEY (`option_title_id`),
  KEY `CATALOG_OGGETTO_OPTION_TITLE_OPTION` (`option_id`),
  KEY `CATALOG_OGGETTO_OPTION_TITLE_STORE` (`store_id`),
  KEY `IDX_CATALOG_OGGETTO_OPTION_TITLE_SI_OI` (`store_id`,`option_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TITLE_OPTION` FOREIGN KEY (`option_id`) REFERENCES `{$installer->getTable('score/oggetto_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TITLE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB default CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('score/oggetto_option_type_value')}`;
CREATE TABLE `{$installer->getTable('score/oggetto_option_type_value')}` (
  `option_type_id` int(10) unsigned NOT NULL auto_increment,
  `option_id` int(10) unsigned NOT NULL default '0',
  `sku` varchar(64) NOT NULL default '',
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (`option_type_id`),
  KEY `CATALOG_OGGETTO_OPTION_TYPE_VALUE_OPTION` (`option_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_VALUE_OPTION` FOREIGN KEY (`option_id`) REFERENCES `{$installer->getTable('score/oggetto_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB default CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('score/oggetto_option_type_price')}`;
CREATE TABLE `{$installer->getTable('score/oggetto_option_type_price')}` (
  `option_type_price_id` int(10) unsigned NOT NULL auto_increment,
  `option_type_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `price` decimal(12,4) NOT NULL default '0.00',
  `price_type` enum('fixed','percent') NOT NULL default 'fixed',
  PRIMARY KEY (`option_type_price_id`),
  KEY `CATALOG_OGGETTO_OPTION_TYPE_PRICE_OPTION_TYPE` (`option_type_id`),
  KEY `CATALOG_OGGETTO_OPTION_TYPE_PRICE_STORE` (`store_id`),
  KEY `IDX_CATALOG_OGGETTO_OPTION_TYPE_PRICE_SI_OTI` (`store_id`,`option_type_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_PRICE_OPTION` FOREIGN KEY (`option_type_id`) REFERENCES `{$installer->getTable('score/oggetto_option_type_value')}` (`option_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_PRICE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB default CHARSET=utf8;

-- DROP TABLE IF EXISTS `{$installer->getTable('score/oggetto_option_type_title')}`;
CREATE TABLE `{$installer->getTable('score/oggetto_option_type_title')}` (
  `option_type_title_id` int(10) unsigned NOT NULL auto_increment,
  `option_type_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY (`option_type_title_id`),
  KEY `CATALOG_OGGETTO_OPTION_TYPE_TITLE_OPTION` (`option_type_id`),
  KEY `CATALOG_OGGETTO_OPTION_TYPE_TITLE_STORE` (`store_id`),
  KEY `IDX_CATALOG_OGGETTO_OPTION_TYPE_TITLE_SI_OTI` (`store_id`,`option_type_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_TITLE_OPTION` FOREIGN KEY (`option_type_id`) REFERENCES `{$installer->getTable('score/oggetto_option_type_value')}` (`option_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_TITLE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
)ENGINE=InnoDB default CHARSET=utf8;


ALTER TABLE `{$installer->getTable('core_url_rewrite')}` ADD INDEX `IDX_CATEGORY_REWRITE` (`category_id`, `is_system`, `oggetto_id`, `store_id`, `id_path`);
");


$installer->run("
CREATE TABLE `{$installer->getTable('score/eav_attribute')}` (
  `attribute_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `frontend_input_renderer` varchar(255) DEFAULT NULL,
  `is_global` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_visible` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_searchable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_filterable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_comparable` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_visible_on_front` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_html_allowed_on_front` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_used_for_price_rules` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_filterable_in_search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `used_in_oggetto_listing` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `used_for_sort_by` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_configurable` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `apply_to` varchar(255) NOT NULL,
  `is_visible_in_advanced_search` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  PRIMARY KEY (`attribute_id`),
  KEY `IDX_USED_FOR_SORT_BY` (`used_for_sort_by`),
  KEY `IDX_USED_IN_OGGETTO_LISTING` (`used_in_oggetto_listing`),
  CONSTRAINT `FK_SCORE_EAV_ATTRIBUTE_ID` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

}

$installer->endSetup();

$installer->installEntities();




// Create Root Score Node
Mage::getModel('score/category')
    ->setStoreId(0)
    ->setId(1)
    ->setPath(1)
    ->setName('Root Catalog')
    ->setInitialSetupFlag(true)
    ->save();

/* @var $category Shaurmalab_Score_Model_Category */
$category = Mage::getModel('score/category');

$category->setStoreId(0)
    ->setName('Default Category')
    ->setDisplayMode('OGGETTOS')
    ->setAttributeSetId($category->getDefaultAttributeSetId())
    ->setIsActive(1)
    ->setPath('1')
    ->setInitialSetupFlag(true)
    ->save();

$installer->setConfigData('score/category/root_id', $category->getId());

$installer->addAttributeGroup('score_oggetto', 'Default', 'Design', 6);

$entityTypeId     = $installer->getEntityTypeId('score_category');
$attributeSetId   = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

// update General Group
$installer->updateAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'attribute_group_name', 'General Information');
$installer->updateAttributeGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'sort_order', '10');

$groups = array(
    'display'   => array(
        'name'  => 'Display Settings',
        'sort'  => 20,
        'id'    => null
    ),
    'design'    => array(
        'name'  => 'Custom Design',
        'sort'  => 30,
        'id'    => null
    )
);

foreach ($groups as $k => $groupProp) {
    $installer->addAttributeGroup($entityTypeId, $attributeSetId, $groupProp['name'], $groupProp['sort']);
    $groups[$k]['id'] = $installer->getAttributeGroupId($entityTypeId, $attributeSetId, $groupProp['name']);
}

// update attributes group and sort
$attributes = array(
    'custom_design'         => array(
        'group' => 'design',
        'sort'  => 10
    ),
    'custom_design_apply'   => array(
        'group' => 'design',
        'sort'  => 20
    ),
    'custom_design_from'    => array(
        'group' => 'design',
        'sort'  => 30
    ),
    'custom_design_to'      => array(
        'group' => 'design',
        'sort'  => 40
    ),
    'page_layout'           => array(
        'group' => 'design',
        'sort'  => 50
    ),
    'custom_layout_update'  => array(
        'group' => 'design',
        'sort'  => 60
    ),
    'display_mode'          => array(
        'group' => 'display',
        'sort'  => 10
    ),
    'landing_page'          => array(
        'group' => 'display',
        'sort'  => 20
    ),
    'is_anchor'             => array(
        'group' => 'display',
        'sort'  => 30
    ),
    'available_sort_by'     => array(
        'group' => 'display',
        'sort'  => 40
    ),
    'default_sort_by'       => array(
        'group' => 'display',
        'sort'  => 50
    ),
);

foreach ($attributes as $attributeCode => $attributeProp) {
    $installer->addAttributeToGroup(
        $entityTypeId,
        $attributeSetId,
        $groups[$attributeProp['group']]['id'],
        $attributeCode,
        $attributeProp['sort']
    );
}

$describe = $installer->getConnection()->describeTable($installer->getTable('score/eav_attribute'));
foreach ($describe as $columnData) {
    if ($columnData['COLUMN_NAME'] == 'attribute_id') {
        continue;
    }
    $installer->getConnection()->dropColumn($installer->getTable('eav/attribute'), $columnData['COLUMN_NAME']);
}
