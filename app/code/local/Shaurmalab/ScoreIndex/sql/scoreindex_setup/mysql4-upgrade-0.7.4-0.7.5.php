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
 * @package     Shaurmalab_ScoreIndex
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */


$installer->run("
    CREATE TABLE `{$installer->getTable('scoreindex_eav_tmp')}` (
        `store_id` smallint(5) unsigned NOT NULL default '0',
        `entity_id` int(10) unsigned NOT NULL default '0',
        `attribute_id` smallint(5) unsigned NOT NULL default '0',
        `value` int(11) NOT NULL default '0'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    insert into `{$installer->getTable('scoreindex_eav_tmp')}`
        select distinct store_id, entity_id, attribute_id, value
        from `{$installer->getTable('scoreindex_eav')}`;

    DROP TABLE `{$installer->getTable('scoreindex_eav')}`;

    CREATE TABLE `{$installer->getTable('scoreindex_eav')}` (
        `store_id` smallint(5) unsigned NOT NULL default '0',
        `entity_id` int(10) unsigned NOT NULL default '0',
        `attribute_id` smallint(5) unsigned NOT NULL default '0',
        `value` int(11) NOT NULL default '0',
        PRIMARY KEY  (`store_id`,`entity_id`,`attribute_id`,`value`),
        KEY `IDX_VALUE` (`value`),
        KEY `FK_SCOREINDEX_EAV_ENTITY` (`entity_id`),
        KEY `FK_SCOREINDEX_EAV_ATTRIBUTE` (`attribute_id`),
        KEY `FK_SCOREINDEX_EAV_STORE` (`store_id`),
        CONSTRAINT `FK_SCOREINDEX_EAV_ATTRIBUTE` FOREIGN KEY (`attribute_id`)
            REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_SCOREINDEX_EAV_ENTITY` FOREIGN KEY (`entity_id`)
            REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT `FK_SCOREINDEX_EAV_STORE` FOREIGN KEY (`store_id`)
            REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    insert into `{$installer->getTable('scoreindex_eav')}`
        select store_id, entity_id, attribute_id, value
        from `{$installer->getTable('scoreindex_eav_tmp')}`;

    DROP TABLE `{$installer->getTable('scoreindex_eav_tmp')}`;






    CREATE TABLE `{$installer->getTable('scoreindex_price_tmp')}` (
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `entity_id` int(10) unsigned NOT NULL default '0',
      `attribute_id` smallint(5) unsigned NOT NULL default '0',
      `customer_group_id` smallint(3) unsigned NOT NULL default '0',
      `qty` decimal(12,4) unsigned NOT NULL default '0.0000',
      `value` decimal(12,4) NOT NULL default '0.0000'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    insert into `{$installer->getTable('scoreindex_price_tmp')}`
        select distinct store_id, entity_id, attribute_id, customer_group_id, qty, value
        from `{$installer->getTable('scoreindex_price')}`;

    DROP TABLE `{$installer->getTable('scoreindex_price')}`;

    CREATE TABLE `{$installer->getTable('scoreindex_price')}` (
      `store_id` smallint(5) unsigned NOT NULL default '0',
      `entity_id` int(10) unsigned NOT NULL default '0',
      `attribute_id` smallint(5) unsigned NOT NULL default '0',
      `customer_group_id` smallint(3) unsigned NOT NULL default '0',
      `qty` decimal(12,4) unsigned NOT NULL default '0.0000',
      `value` decimal(12,4) NOT NULL default '0.0000',
      KEY `IDX_VALUE` (`value`),
      KEY `IDX_QTY` (`qty`),
      KEY `FK_SCOREINDEX_PRICE_ENTITY` (`entity_id`),
      KEY `FK_SCOREINDEX_PRICE_ATTRIBUTE` (`attribute_id`),
      KEY `FK_SCOREINDEX_PRICE_STORE` (`store_id`),
      KEY `FK_SCOREINDEX_PRICE_CUSTOMER_GROUP` (`customer_group_id`),
      KEY `IDX_RANGE_VALUE` (`store_id`, `entity_id`,`attribute_id`,`customer_group_id`,`value`),
      CONSTRAINT `FK_SCOREINDEX_PRICE_ATTRIBUTE` FOREIGN KEY (`attribute_id`)
        REFERENCES `{$installer->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_SCOREINDEX_PRICE_ENTITY` FOREIGN KEY (`entity_id`)
        REFERENCES `{$installer->getTable('score_oggetto_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_SCOREINDEX_PRICE_STORE` FOREIGN KEY (`store_id`)
        REFERENCES `{$installer->getTable('core_store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    insert into `{$installer->getTable('scoreindex_price')}`
        select store_id, entity_id, attribute_id, customer_group_id, qty, value
        from `{$installer->getTable('scoreindex_price_tmp')}`;

    DROP TABLE `{$installer->getTable('scoreindex_price_tmp')}`;
");

