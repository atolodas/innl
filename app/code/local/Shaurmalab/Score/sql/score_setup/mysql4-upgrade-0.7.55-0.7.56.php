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

$categoryIndexTable = $installer->getTable('score/category_oggetto_index');

$installer->getConnection()->addColumn(
    $categoryIndexTable,
    'store_id',
    'smallint(5) unsigned NOT NULL default \'0\''
);

$installer->getConnection()->addColumn(
    $categoryIndexTable,
    'visibility',
    'tinyint(3) unsigned NOT NULL'
);


/**
 * Clear relation with root category
 */
$installer->getConnection()->delete($categoryIndexTable, 'category_id='.Shaurmalab_Score_Model_Category::TREE_ROOT_ID);
$installer->getConnection()->addKey(
    $categoryIndexTable,
    'FK_SCORE_CATEGORY_OGGETTO_INDEX_CATEGORY_ENTITY',
    array('category_id')
);
$installer->getConnection()->dropKey($categoryIndexTable, 'IDX_CATEGORY_POSITION');
$installer->getConnection()->dropKey($categoryIndexTable, 'UNQ_CATEGORY_OGGETTO');

$storesData = $installer->getConnection()->fetchAll("
    SELECT
        s.store_id, s.website_id, c.path AS root_path
    FROM
        {$installer->getTable('core/store')} AS s,
        {$installer->getTable('core/store_group')} AS sg,
        {$installer->getTable('score/category')} AS c
    WHERE
        sg.group_id=s.group_id
        AND c.entity_id=sg.root_category_id
");

foreach ($storesData as $storeData) {
    $storeId   = $storeData['store_id'];
    $websiteId = $storeData['website_id'];
    $path      = $storeData['root_path'];

    $query = "INSERT INTO {$categoryIndexTable}
       (`category_id`, `oggetto_id`, `position`, `is_parent`, `store_id`, `visibility`)
    SELECT
       ci.category_id,
       ci.oggetto_id,
       ci.position,
       ci.is_parent,
       {$storeId},
       ep.visibility
    FROM
       $categoryIndexTable AS ci
       INNER JOIN {$installer->getTable('score/oggetto_website')} AS pw
           ON pw.oggetto_id=ci.oggetto_id AND pw.website_id={$websiteId}
       INNER JOIN {$installer->getTable('score/category')} AS c
           ON c.entity_id=ci.category_id AND c.path LIKE '{$path}%'
       INNER JOIN {$installer->getTable('score/oggetto_enabled_index')} AS ep
           ON ep.oggetto_id=ci.oggetto_id AND ep.store_id={$storeId}
    WHERE
        ci.store_id=0";

    $installer->run($query);
}

$installer->getConnection()->delete($categoryIndexTable, 'store_id=0');

$installer->getConnection()->addKey(
    $categoryIndexTable,
    'UNQ_CATEGORY_OGGETTO',
    array('store_id', 'category_id', 'oggetto_id')
);

$installer->getConnection()->addKey(
    $categoryIndexTable,
    'IDX_JOIN',
    array('oggetto_id', 'store_id', 'category_id', 'visibility')
);

$installer->getConnection()->addKey(
    $categoryIndexTable,
    'IDX_BASE',
    array('store_id', 'category_id', 'visibility', 'is_parent', 'position')
);

$installer->getConnection()->addConstraint(
    'FK_CATEGORY_OGGETTO_INDEX_STORE',
    $categoryIndexTable,
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->endSetup();
