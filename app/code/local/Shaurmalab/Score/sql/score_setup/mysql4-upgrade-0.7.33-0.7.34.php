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

$select = $installer->getConnection()->select()
    ->from($installer->getTable('score_category_oggetto'), array(
        'category_id',
        'oggetto_id',
        'position',
        'cnt' => 'COUNT(oggetto_id)'
    ))
    ->group('category_id')
    ->group('oggetto_id')
    ->having('cnt > 1');
$rowSet = $installer->getConnection()->fetchAll($select);

foreach ($rowSet as $row) {
    $data = array(
        'category_id'   => $row['category_id'],
        'oggetto_id'    => $row['oggetto_id'],
        'position'      => $row['position']
    );
    $installer->getConnection()->delete($installer->getTable('score_category_oggetto'), array(
        $installer->getConnection()->quoteInto('category_id = ?', $row['category_id']),
        $installer->getConnection()->quoteInto('oggetto_id = ?', $row['oggetto_id'])
    ));
    $installer->getConnection()->insert($installer->getTable('score_category_oggetto'), $data);
}

$installer->run("
ALTER TABLE `{$installer->getTable('score_category_oggetto')}`
    ADD UNIQUE `UNQ_CATEGORY_OGGETTO` (`category_id`, `oggetto_id`);
");

$installer->endSetup();
