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

/** @var $installer Shaurmalab_Score_Model_Resource_Setup */
$installer  = $this;
$connection = $installer->getConnection();

$connection->addIndex(
    $installer->getTable('score/category_oggetto_indexer_tmp'),
    $installer->getIdxName('score/category_oggetto_indexer_tmp', array('oggetto_id', 'category_id', 'store_id')),
    array('oggetto_id', 'category_id', 'store_id')
);

$table = $installer->getTable('score/category_oggetto_enabled_indexer_idx');
$connection->dropIndex($table, 'IDX_CATALOG_CATEGORY_OGGETTO_INDEX_ENBL_IDX_OGGETTO_ID');
$connection->addIndex(
    $table,
    $installer->getIdxName('score/category_oggetto_enabled_indexer_idx', array('oggetto_id', 'visibility')),
    array('oggetto_id', 'visibility')
);


$table = $installer->getTable('score/category_oggetto_enabled_indexer_tmp');
$connection->dropIndex($table, 'IDX_CATALOG_CATEGORY_OGGETTO_INDEX_ENBL_TMP_OGGETTO_ID');
$connection->addIndex(
    $table,
    $installer->getIdxName('score/category_oggetto_enabled_indexer_tmp', array('oggetto_id', 'visibility')),
    array('oggetto_id', 'visibility')
);

$connection->addIndex(
    $installer->getTable('score/category_anchor_oggettos_indexer_idx'),
    $installer->getIdxName(
        'score/category_anchor_oggettos_indexer_idx',
        array('category_id', 'oggetto_id', 'position')
    ),
    array('category_id', 'oggetto_id', 'position')
);

$connection->addIndex(
    $installer->getTable('score/category_anchor_oggettos_indexer_tmp'),
    $installer->getIdxName(
        'score/category_anchor_oggettos_indexer_tmp',
        array('category_id', 'oggetto_id', 'position')
    ),
    array('category_id', 'oggetto_id', 'position')
);

$connection->addIndex(
    $installer->getTable('score/category_anchor_indexer_idx'),
    $installer->getIdxName(
        'score/category_anchor_indexer_idx',
        array('path', 'category_id')
    ),
    array('path', 'category_id')
);

$connection->addIndex(
    $installer->getTable('score/category_anchor_indexer_tmp'),
    $installer->getIdxName(
        'score/category_anchor_indexer_tmp',
        array('path', 'category_id')
    ),
    array('path', 'category_id')
);

$connection->addIndex(
    $installer->getTable('score/category'),
    $installer->getIdxName(
        'score/category',
        array('path', 'entity_id')
    ),
    array('path', 'entity_id')
);
