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

/**
 * Create table 'scoretag/scoretag'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('scoretag/scoretag'))
    ->addColumn('scoretag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Scoretag Id')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Name')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Status')
    ->addColumn('first_customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'First Customer Id')
    ->addColumn('first_store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'First Store Id')
    ->addForeignKey($installer->getFkName('scoretag/scoretag', 'first_customer_id', 'customer/entity', 'entity_id'),
        'first_customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->addForeignKey($installer->getFkName('scoretag/scoretag', 'first_store_id', 'core/store', 'store_id'),
        'first_store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->setComment('Scoretag');
$installer->getConnection()->createTable($table);

/**
 * Create table 'scoretag/relation'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('scoretag/relation'))
    ->addColumn('scoretag_relation_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Scoretag Relation Id')
    ->addColumn('scoretag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Scoretag Id')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Customer Id')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Store Id')
    ->addColumn('active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Active')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Created At')
    ->addIndex($installer->getIdxName('scoretag/relation', array('scoretag_id', 'customer_id', 'oggetto_id', 'store_id'), Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE),
        array('scoretag_id', 'customer_id', 'oggetto_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('scoretag/relation', array('oggetto_id')),
        array('oggetto_id'))
    ->addIndex($installer->getIdxName('scoretag/relation', array('scoretag_id')),
        array('scoretag_id'))
    ->addIndex($installer->getIdxName('scoretag/relation', array('customer_id')),
        array('customer_id'))
    ->addIndex($installer->getIdxName('scoretag/relation', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('scoretag/relation', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('scoretag/relation', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('scoretag/relation', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('scoretag/relation', 'scoretag_id', 'scoretag/scoretag', 'scoretag_id'),
        'scoretag_id', $installer->getTable('scoretag/scoretag'), 'scoretag_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Scoretag Relation');
$installer->getConnection()->createTable($table);

/**
 * Create table 'scoretag/summary'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('scoretag/summary'))
    ->addColumn('scoretag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Scoretag Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('customers', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customers')
    ->addColumn('oggettos', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggettos')
    ->addColumn('uses', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Uses')
    ->addColumn('historical_uses', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Historical Uses')
    ->addColumn('popularity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Popularity')
    ->addColumn('base_popularity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Base Popularity')
    ->addIndex($installer->getIdxName('scoretag/summary', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('scoretag/summary', array('scoretag_id')),
        array('scoretag_id'))
    ->addForeignKey($installer->getFkName('scoretag/summary', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('scoretag/summary', 'scoretag_id', 'scoretag/scoretag', 'scoretag_id'),
        'scoretag_id', $installer->getTable('scoretag/scoretag'), 'scoretag_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Scoretag Summary');
$installer->getConnection()->createTable($table);

/**
 * Create table 'scoretag/properties'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('scoretag/properties'))
    ->addColumn('scoretag_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Scoretag Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store Id')
    ->addColumn('base_popularity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Base Popularity')
    ->addIndex($installer->getIdxName('scoretag/properties', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('scoretag/properties', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('scoretag/properties', 'scoretag_id', 'scoretag/scoretag', 'scoretag_id'),
        'scoretag_id', $installer->getTable('scoretag/scoretag'), 'scoretag_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Scoretag Properties');
$installer->getConnection()->createTable($table);


$installer->endSetup();
