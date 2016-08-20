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

$installer->getConnection()->dropForeignKey(
    $installer->getTable('score_oggetto_entity_datetime'),
    'FK_SCORE_OGGETTO_ENTITY_DATETIME_OGGETTO_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('score_oggetto_entity_decimal'),
    'FK_SCORE_OGGETTO_ENTITY_DECIMAL_OGGETTO_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('score_oggetto_entity_varchar'),
    'FK_SCORE_OGGETTO_ENTITY_VARCHAR_OGGETTO_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('score_oggetto_entity_tier_price'),
    'FK_SCORE_OGGETTO_ENTITY_TIER_PRICE_OGGETTO_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('score_category_oggetto_index'),
    'FK_SCORE_CATEGORY_OGGETTO_INDEX_CATEGORY_ENTITY'
);
$installer->getConnection()->dropForeignKey(
    $installer->getTable('score_category_oggetto_index'),
    'FK_SCORE_CATEGORY_OGGETTO_INDEX_OGGETTO_ENTITY'
);

$installer->getConnection()->addConstraint('FK_SCORE_PROD_ENTITY_DATETIME_PROD_ENTITY',
    $installer->getTable('score_oggetto_entity_datetime'), 'entity_id',
    $installer->getTable('score_oggetto_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_SCORE_PROD_ENTITY_DECIMAL_PROD_ENTITY',
    $installer->getTable('score_oggetto_entity_decimal'), 'entity_id',
    $installer->getTable('score_oggetto_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_SCORE_PROD_ENTITY_VARCHAR_PROD_ENTITY',
    $installer->getTable('score_oggetto_entity_varchar'), 'entity_id',
    $installer->getTable('score_oggetto_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_SCORE_PROD_ENTITY_TIER_PRICE_PROD_ENTITY',
    $installer->getTable('score_oggetto_entity_tier_price'), 'entity_id',
    $installer->getTable('score_oggetto_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_SCORE_CATEGORY_PROD_IDX_CATEGORY_ENTITY',
    $installer->getTable('score_category_oggetto_index'), 'category_id',
    $installer->getTable('score_category_entity'), 'entity_id'
);
$installer->getConnection()->addConstraint('FK_SCORE_CATEGORY_PROD_IDX_PROD_ENTITY',
    $installer->getTable('score_category_oggetto_index'), 'oggetto_id',
    $installer->getTable('score_oggetto_entity'), 'entity_id'
);

$installer->endSetup();
