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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoretag/properties'),
    'FK_TAG_PROPERTIES_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoretag/properties'),
    'FK_TAG_PROPERTIES_TAG'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoretag/relation'),
    'FK_TAG_RELATION_CUSTOMER'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoretag/relation'),
    'FK_TAG_RELATION_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoretag/relation'),
    'FK_TAG_RELATION_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoretag/relation'),
    'FK_TAG_RELATION_TAG'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoretag/summary'),
    'FK_TAG_SUMMARY_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoretag/summary'),
    'FK_TAG_SUMMARY_TAG'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('scoretag/relation'),
    'UNQ_TAG_CUSTOMER_PRODUCT_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoretag/relation'),
    'IDX_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoretag/relation'),
    'IDX_TAG'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoretag/relation'),
    'IDX_CUSTOMER'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoretag/relation'),
    'IDX_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoretag/properties'),
    'FK_TAG_PROPERTIES_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoretag/summary'),
    'FK_TAG_SUMMARY_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoretag/summary'),
    'IDX_TAG'
);


/**
 * Change columns
 */
$tables = array(
    $installer->getTable('scoretag/scoretag') => array(
        'columns' => array(
            'scoretag_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Scoretag Id'
            ),
            'name' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Name'
            ),
            'status' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Status'
            ),
            'first_customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'First Customer Id'
            ),
            'first_store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'comment'   => 'First Store Id'
            )
        ),
        'comment' => 'Scoretag'
    ),
    $installer->getTable('scoretag/relation') => array(
        'columns' => array(
            'scoretag_relation_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Scoretag Relation Id'
            ),
            'scoretag_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Scoretag Id'
            ),
            'customer_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'comment'   => 'Customer Id'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggetto Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Store Id'
            ),
            'active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Active'
            ),
            'created_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'comment'   => 'Created At'
            )
        ),
        'comment' => 'Scoretag Relation'
    ),
    $installer->getTable('scoretag/summary') => array(
        'columns' => array(
            'scoretag_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Scoretag Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'customers' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Customers'
            ),
            'oggettos' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Oggettos'
            ),
            'uses' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Uses'
            ),
            'historical_uses' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Historical Uses'
            ),
            'popularity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Popularity'
            ),
            'base_popularity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Base Popularity'
            )
        ),
        'comment' => 'Scoretag Summary'
    ),
    $installer->getTable('scoretag/properties') => array(
        'columns' => array(
            'scoretag_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Scoretag Id'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'default'   => '0',
                'comment'   => 'Store Id'
            ),
            'base_popularity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Base Popularity'
            )
        ),
        'comment' => 'Scoretag Properties'
    )
);

$installer->getConnection()->modifyTables($tables);


/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('scoretag/properties'),
    $installer->getIdxName('scoretag/properties', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoretag/relation'),
    $installer->getIdxName(
        'scoretag/relation',
        array('scoretag_id', 'customer_id', 'oggetto_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('scoretag_id', 'customer_id', 'oggetto_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoretag/relation'),
    $installer->getIdxName('scoretag/relation', array('oggetto_id')),
    array('oggetto_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoretag/relation'),
    $installer->getIdxName('scoretag/relation', array('scoretag_id')),
    array('scoretag_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoretag/relation'),
    $installer->getIdxName('scoretag/relation', array('customer_id')),
    array('customer_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoretag/relation'),
    $installer->getIdxName('scoretag/relation', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoretag/summary'),
    $installer->getIdxName('scoretag/summary', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoretag/summary'),
    $installer->getIdxName('scoretag/summary', array('scoretag_id')),
    array('scoretag_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/scoretag', 'first_customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('scoretag/scoretag'),
    'first_customer_id',
    $installer->getTable('customer/entity'),
    'entity_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/scoretag', 'first_store_id', 'core/store', 'store_id'),
    $installer->getTable('scoretag/scoretag'),
    'first_store_id',
    $installer->getTable('core/store'),
    'store_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
    Varien_Db_Ddl_Table::ACTION_NO_ACTION
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/properties', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('scoretag/properties'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/properties', 'scoretag_id', 'scoretag/scoretag', 'scoretag_id'),
    $installer->getTable('scoretag/properties'),
    'scoretag_id',
    $installer->getTable('scoretag/scoretag'),
    'scoretag_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/relation', 'customer_id', 'customer/entity', 'entity_id'),
    $installer->getTable('scoretag/relation'),
    'customer_id',
    $installer->getTable('customer/entity'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/relation', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('scoretag/relation'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/relation', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('scoretag/relation'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/relation', 'scoretag_id', 'scoretag/scoretag', 'scoretag_id'),
    $installer->getTable('scoretag/relation'),
    'scoretag_id',
    $installer->getTable('scoretag/scoretag'),
    'scoretag_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/summary', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('scoretag/summary'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoretag/summary', 'scoretag_id', 'scoretag/scoretag', 'scoretag_id'),
    $installer->getTable('scoretag/summary'),
    'scoretag_id',
    $installer->getTable('scoretag/scoretag'),
    'scoretag_id'
);

$installer->endSetup();
