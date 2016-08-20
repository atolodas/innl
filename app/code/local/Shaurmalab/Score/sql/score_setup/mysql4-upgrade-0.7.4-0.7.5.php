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
DROP TABLE IF EXISTS {$this->getTable('score_oggetto_website')};
CREATE TABLE {$this->getTable('score_oggetto_website')} (
  `oggetto_id` INTEGER(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `website_id` SMALLINT(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`oggetto_id`, `website_id`),
  CONSTRAINT `FK_SCORE_OGGETTO_WEBSITE_WEBSITE` FOREIGN KEY `FK_SCORE_OGGETTO_WEBSITE_WEBSITE` (`website_id`)
    REFERENCES `{$this->getTable('core_website')}` (`website_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_SCORE_WEBSITE_OGGETTO_OGGETTO` FOREIGN KEY `FK_SCORE_WEBSITE_OGGETTO_OGGETTO` (`oggetto_id`)
    REFERENCES `{$this->getTable('score/oggetto')}` (`entity_id`) ON DELETE CASCADE
);

DROP TABLE IF EXISTS {$this->getTable('score_oggetto_status')};
DROP TABLE IF EXISTS {$this->getTable('score_oggetto_visibility')};
DROP TABLE IF EXISTS {$this->getTable('score_oggetto_type')};
");
$installer->endSetup();
$oggettoTable = $this->getTable('score_oggetto_entity');
$installer->getConnection()->dropColumn($oggettoTable, 'parent_id');
$installer->getConnection()->dropColumn($oggettoTable, 'store_id');
$installer->getConnection()->dropColumn($oggettoTable, 'is_active');

try {
    $installer->run("
    INSERT INTO {$this->getTable('score_oggetto_website')}
        SELECT DISTINCT ps.oggetto_id, cs.website_id
        FROM {$this->getTable('score_oggetto_store')} ps, {$this->getTable('core_store')} cs
        WHERE cs.store_id=ps.store_id AND ps.store_id>0;
    DROP TABLE IF EXISTS {$this->getTable('score_oggetto_store')};
    ");
} catch (Exception $e) {
}

$categoryTable = $this->getTable('score/category');
$installer->getConnection()->dropForeignKey($categoryTable, 'FK_SCORE_CATEGORY_ENTITY_TREE_NODE');

try {
    $this->run("ALTER TABLE `{$this->getTable('score/category')}` ADD `path` VARCHAR( 255 ) NOT NULL, ADD `position` INT NOT NULL;");
} catch (Exception $e) {
}
try {
    $this->run("DROP TABLE IF EXISTS `{$this->getTable('score/category_tree')}`;");
} catch (Exception $e) {
}

$installer->getConnection()->dropKey($categoryTable, 'FK_score_category_ENTITY_ENTITY_TYPE');
$installer->getConnection()->dropKey($categoryTable, 'FK_score_category_ENTITY_STORE');
$installer->getConnection()->dropColumn($categoryTable, 'store_id');

$tierPriceTable = $this->getTable('score_oggetto_entity_tier_price');
$installer->getConnection()->dropColumn($tierPriceTable, 'entity_type_id');
$installer->getConnection()->dropColumn($tierPriceTable, 'attribute_id');
$installer->getConnection()->dropForeignKey($tierPriceTable, 'FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_ATTRIBUTE');
$installer->getConnection()->dropKey($tierPriceTable, 'FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_ATTRIBUTE');

$installer->startSetup();
$installer->installEntities();
$installer->endSetup();

$this->convertOldTreeToNew();
