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
 * @package     Mage_Scoretag
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

$purgeFk = array(
    $installer->getTable('scoretag/relation') => array(
        'oggetto_id', 'scoretag_id', 'customer_id', 'store_id'
    ),
    $installer->getTable('scoretag/summary') => array(
        'scoretag_id'
    ),
);
$purgeIndex = array(
    array(
        $installer->getTable('scoretag/relation'),
        array('oggetto_id')
    ),
    array(
        $installer->getTable('scoretag/relation'),
        array('scoretag_id')
    ),
    array(
        $installer->getTable('scoretag/relation'),
        array('customer_id')
    ),
    array(
        $installer->getTable('scoretag/relation'),
        array('store_id')
    ),
    array(
        $installer->getTable('scoretag/summary'),
        array('scoretag_id')
    ),
);
foreach ($purgeFk as $tableName => $columns) {
    $foreignKeys = $installer->getConnection()->getForeignKeys($tableName);
    foreach ($foreignKeys as $fkProp) {
        if (in_array($fkProp['COLUMN_NAME'], $columns)) {
            $installer->getConnection()
                ->dropForeignKey($tableName, $fkProp['FK_NAME']);
        }
    }
}

foreach ($purgeIndex as $prop) {
    list($tableName, $columns) = $prop;
    $indexList = $installer->getConnection()->getIndexList($tableName);
    foreach ($indexList as $indexProp) {
        if ($columns === $indexProp['COLUMNS_LIST']) {
            $installer->getConnection()->dropKey($tableName, $indexProp['KEY_NAME']);
        }
    }
}

$installer->getConnection()->addKey($installer->getTable('scoretag/relation'),
    'IDX_PRODUCT', 'oggetto_id');
$installer->getConnection()->addKey($installer->getTable('scoretag/relation'),
    'IDX_TAG', 'scoretag_id');
$installer->getConnection()->addKey($installer->getTable('scoretag/relation'),
    'IDX_CUSTOMER', 'customer_id');
$installer->getConnection()->addKey($installer->getTable('scoretag/relation'),
    'IDX_STORE', 'store_id');
$installer->getConnection()->addKey($installer->getTable('scoretag/summary'),
    'IDX_TAG', 'scoretag_id');

$installer->getConnection()->addConstraint('FK_TAG_RELATION_PRODUCT',
    $installer->getTable('scoretag/relation'), 'oggetto_id',
    $installer->getTable('score/oggetto'), 'entity_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_TAG_RELATION_TAG',
    $installer->getTable('scoretag/relation'), 'scoretag_id',
    $installer->getTable('scoretag/scoretag'), 'scoretag_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_TAG_RELATION_CUSTOMER',
    $installer->getTable('scoretag/relation'), 'customer_id',
    $installer->getTable('customer/entity'), 'entity_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_TAG_RELATION_STORE',
    $installer->getTable('scoretag/relation'), 'store_id',
    $installer->getTable('core/store'), 'store_id',
    'CASCADE', 'CASCADE', true);
$installer->getConnection()->addConstraint('FK_TAG_SUMMARY_TAG',
    $installer->getTable('scoretag/summary'), 'scoretag_id',
    $installer->getTable('scoretag/scoretag'), 'scoretag_id',
    'CASCADE', 'CASCADE', true);
$installer->endSetup();
