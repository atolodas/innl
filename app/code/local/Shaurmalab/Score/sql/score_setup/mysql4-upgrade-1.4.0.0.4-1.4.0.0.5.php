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
$groupedLinkType = Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED;
$installer->run("

CREATE TABLE `{$installer->getTable('score/oggetto_relation')}` (
  `parent_id` INT(10) UNSIGNED NOT NULL,
  `child_id` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY  (`parent_id`,`child_id`),
  KEY `IDX_CHILD` (`child_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_RELATION_CHILD` FOREIGN KEY (`child_id`) REFERENCES `{$installer->getTable('score/oggetto')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_RELATION_PARENT` FOREIGN KEY (`parent_id`) REFERENCES `{$installer->getTable('score/oggetto')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=utf8 ROW_FORMAT=FIXED;

INSERT IGNORE INTO `{$installer->getTable('score/oggetto_relation')}`
SELECT
  `oggetto_id`,
  `linked_oggetto_id`
FROM `{$installer->getTable('score/oggetto_link')}`
WHERE `link_type_id`={$groupedLinkType};

INSERT IGNORE INTO `{$installer->getTable('score/oggetto_relation')}`
SELECT
  `parent_id`,
  `oggetto_id`
FROM `{$installer->getTable('score/oggetto_super_link')}`;

CREATE TABLE `{$installer->getTable('score/oggetto_index_eav')}` (
  `entity_id` int(10) unsigned NOT NULL,
  `attribute_id` smallint(5) unsigned NOT NULL,
  `store_id` smallint(5) unsigned NOT NULL,
  `value` int(10) unsigned NOT NULL,
  PRIMARY KEY (`entity_id`,`attribute_id`,`store_id`,`value`),
  KEY `IDX_ENTITY` (`entity_id`),
  KEY `IDX_ATTRIBUTE` (`attribute_id`),
  KEY `IDX_STORE` (`store_id`),
  KEY `IDX_VALUE` (`value`),
  CONSTRAINT `FK_SCORE_OGGETTO_INDEX_EAV_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$installer->getTable('eav/attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_INDEX_EAV_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$installer->getTable('score/oggetto')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_OGGETTO_INDEX_EAV_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$installer->getTable('core/store')}` (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");
$installer->endSetup();
