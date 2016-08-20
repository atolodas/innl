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

$installer->run("
    DROP TABLE IF EXISTS `{$this->getTable('score/oggetto_option')}`;
    CREATE TABLE `{$this->getTable('score/oggetto_option')}` (
      `option_id` int(10) unsigned NOT NULL auto_increment,
      `oggetto_id` int(10) unsigned NOT NULL default '0',
      `type` varchar(50) NOT NULL default '',
      `is_require` tinyint(1) NOT NULL default '1',
      `sku` varchar(64) NOT NULL default '',
      `max_characters` int(10) unsigned default NULL,
      `file_extension` varchar(50) default NULL,
      `sort_order` int(10) unsigned NOT NULL default '0',
      PRIMARY KEY (`option_id`),
      KEY `CATALOG_OGGETTO_OPTION_OGGETTO` (`oggetto_id`),
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_OGGETTO` FOREIGN KEY (`oggetto_id`) REFERENCES `{$this->getTable('score/oggetto')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
    )ENGINE=InnoDB default CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('score/oggetto_option_price')}`;
    CREATE TABLE `{$this->getTable('score/oggetto_option_price')}` (
      `option_price_id` int(10) unsigned NOT NULL auto_increment,
      `option_id` int(10) unsigned NOT NULL default '0',
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `price` decimal(12,4) NOT NULL default '0.00',
      `price_type` enum('fixed', 'percent') NOT NULL default 'fixed',
      PRIMARY KEY (`option_price_id`),
      KEY `CATALOG_OGGETTO_OPTION_PRICE_OPTION` (`option_id`),
      KEY `CATALOG_OGGETTO_OPTION_TITLE_STORE` (`store_id`),
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_PRICE_OPTION` FOREIGN KEY (`option_id`) REFERENCES `{$this->getTable('score/oggetto_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_PRICE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
    )ENGINE=InnoDB default CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('score/oggetto_option_title')}`;
    CREATE TABLE `{$this->getTable('score/oggetto_option_title')}` (
      `option_title_id` int(10) unsigned NOT NULL auto_increment,
      `option_id` int(10) unsigned NOT NULL default '0',
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `title` VARCHAR(50) NOT NULL default '',
      PRIMARY KEY (`option_title_id`),
      KEY `CATALOG_OGGETTO_OPTION_TITLE_OPTION` (`option_id`),
      KEY `CATALOG_OGGETTO_OPTION_TITLE_STORE` (`store_id`),
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TITLE_OPTION` FOREIGN KEY (`option_id`) REFERENCES `{$this->getTable('score/oggetto_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TITLE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
    )ENGINE=InnoDB default CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('score/oggetto_option_type_value')}`;
    CREATE TABLE `{$this->getTable('score/oggetto_option_type_value')}` (
      `option_type_id` int(10) unsigned NOT NULL auto_increment,
      `option_id` int(10) unsigned NOT NULL default '0',
      `sku` varchar(64) NOT NULL default '',
      PRIMARY KEY (`option_type_id`),
      KEY `CATALOG_OGGETTO_OPTION_TYPE_VALUE_OPTION` (`option_id`),
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_VALUE_OPTION` FOREIGN KEY (`option_id`) REFERENCES `{$this->getTable('score/oggetto_option')}` (`option_id`) ON DELETE CASCADE ON UPDATE CASCADE
    )ENGINE=InnoDB default CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('score/oggetto_option_type_price')}`;
    CREATE TABLE `{$this->getTable('score/oggetto_option_type_price')}` (
      `option_type_price_id` int(10) unsigned NOT NULL auto_increment,
      `option_type_id` int(10) unsigned NOT NULL default '0',
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `price` decimal(12,4) NOT NULL default '0.00',
      `price_type` enum('fixed','percent') NOT NULL default 'fixed',
      PRIMARY KEY (`option_type_price_id`),
      KEY `CATALOG_OGGETTO_OPTION_TYPE_PRICE_OPTION_TYPE` (`option_type_id`),
      KEY `CATALOG_OGGETTO_OPTION_TYPE_PRICE_STORE` (`store_id`),
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_PRICE_OPTION` FOREIGN KEY (`option_type_id`) REFERENCES `{$this->getTable('score/oggetto_option_type_value')}` (`option_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_PRICE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
    )ENGINE=InnoDB default CHARSET=utf8;

    DROP TABLE IF EXISTS `{$this->getTable('score/oggetto_option_type_title')}`;
    CREATE TABLE `{$this->getTable('score/oggetto_option_type_title')}` (
      `option_type_title_id` int(10) unsigned NOT NULL auto_increment,
      `option_type_id` int(10) unsigned NOT NULL default '0',
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `title` varchar(50) NOT NULL default '',
      PRIMARY KEY (`option_type_title_id`),
      KEY `CATALOG_OGGETTO_OPTION_TYPE_TITLE_OPTION` (`option_type_id`),
      KEY `CATALOG_OGGETTO_OPTION_TYPE_TITLE_STORE` (`store_id`),
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_TITLE_OPTION` FOREIGN KEY (`option_type_id`) REFERENCES `{$this->getTable('score/oggetto_option_type_value')}` (`option_type_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_SCORE_OGGETTO_OPTION_TYPE_TITLE_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
    )ENGINE=InnoDB default CHARSET=utf8;
");

$installer->endSetup();
