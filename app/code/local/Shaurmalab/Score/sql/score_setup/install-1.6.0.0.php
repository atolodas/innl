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
/* @var $installer Shaurmalab_Score_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'score/oggetto'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute Set ID')
    ->addColumn('type_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => Shaurmalab_Score_Model_Oggetto_Type::DEFAULT_TYPE,
        ), 'Type ID')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        ), 'SKU')
    ->addColumn('has_options', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Has Options')
    ->addColumn('required_options', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Required Options')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Update Time')
    ->addIndex($installer->getIdxName('score/oggetto', array('entity_type_id')),
        array('entity_type_id'))
    ->addIndex($installer->getIdxName('score/oggetto', array('attribute_set_id')),
        array('attribute_set_id'))
    ->addIndex($installer->getIdxName('score/oggetto', array('sku')),
        array('sku'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto',
            'attribute_set_id',
            'eav/attribute_set',
            'attribute_set_id'
        ),
        'attribute_set_id', $installer->getTable('eav/attribute_set'), 'attribute_set_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/oggetto', 'entity_type_id', 'eav/entity_type', 'entity_type_id'),
        'entity_type_id', $installer->getTable('eav/entity_type'), 'entity_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Table');
$installer->getConnection()->createTable($table);



/**
 * Create table array('score/oggetto', 'datetime')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/oggetto', 'datetime')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/oggetto', 'datetime'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'datetime'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'datetime'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'datetime'), array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(
            array('score/oggetto', 'datetime'),
            'attribute_id',
            'eav/attribute',
            'attribute_id'
         ),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('score/oggetto', 'datetime'),
            'entity_id',
            'score/oggetto',
            'entity_id'
        ),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('score/oggetto', 'datetime'),
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Datetime Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('score/oggetto', 'decimal')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/oggetto', 'decimal')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/oggetto', 'decimal'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'decimal'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'decimal'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'decimal'), array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey(
        $installer->getFkName(
            array('score/oggetto', 'decimal'),
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('score/oggetto', 'decimal'),
            'entity_id',
            'score/oggetto',
            'entity_id'
        ),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName(array('score/oggetto', 'decimal'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Decimal Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('score/oggetto', 'int')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/oggetto', 'int')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/oggetto', 'int'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'int'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'int'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'int'), array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(
            array('score/oggetto', 'int'),
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('score/oggetto', 'int'),
            'entity_id',
            'score/oggetto',
            'entity_id'
        ),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            array('score/oggetto', 'int'),
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Integer Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('score/oggetto', 'text')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/oggetto', 'text')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/oggetto', 'text'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'text'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'text'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'text'), array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(array('score/oggetto', 'text'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/oggetto', 'text'), 'entity_id', 'score/oggetto', 'entity_id'),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName(array('score/oggetto', 'text'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Text Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('score/oggetto', 'varchar')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/oggetto', 'varchar')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/oggetto', 'varchar'),
            array('entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'varchar'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'varchar'), array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'varchar'), array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(array('score/oggetto', 'varchar'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/oggetto', 'varchar'), 'entity_id', 'score/oggetto', 'entity_id'),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/oggetto', 'varchar'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Varchar Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('score/oggetto', 'gallery')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/oggetto', 'gallery')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Position')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/oggetto', 'gallery'),
            array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'gallery'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'gallery'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/oggetto', 'gallery'), array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(array('score/oggetto', 'gallery'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/oggetto', 'gallery'), 'entity_id', 'score/oggetto', 'entity_id'),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/oggetto', 'gallery'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Gallery Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_set_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attriute Set ID')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Parent Category ID')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        ), 'Update Time')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => false,
        ), 'Tree Path')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Position')
    ->addColumn('level', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Tree Level')
    ->addColumn('children_count', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        ), 'Child Count')
    ->addIndex($installer->getIdxName('score/category', array('level')),
        array('level'))
    ->setComment('Catalog Category Table');
$installer->getConnection()->createTable($table);


/**
 * Create table array('score/category', 'datetime')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/category', 'datetime')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/category', 'datetime'),
            array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/category', 'datetime'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'datetime'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'datetime'), array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'datetime'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'datetime'), 'entity_id', 'score/category', 'entity_id'),
        'entity_id', $installer->getTable('score/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'datetime'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Category Datetime Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('score/category', 'decimal')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/category', 'decimal')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/category', 'decimal'),
            array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/category', 'decimal'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'decimal'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'decimal'), array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'decimal'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'decimal'), 'entity_id', 'score/category', 'entity_id'),
        'entity_id', $installer->getTable('score/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'decimal'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Category Decimal Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('score/category', 'int')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/category', 'int')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/category', 'int'),
            array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/category', 'int'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'int'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'int'), array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'int'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'int'), 'entity_id', 'score/category', 'entity_id'),
        'entity_id', $installer->getTable('score/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'int'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Category Integer Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('score/category', 'text')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/category', 'text')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/category', 'text'),
            array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/category', 'text'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'text'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'text'), array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'text'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'text'), 'entity_id', 'score/category', 'entity_id'),
        'entity_id', $installer->getTable('score/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'text'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Category Text Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table array('score/category', 'varchar')
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable(array('score/category', 'varchar')))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity Type ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            array('score/category', 'varchar'),
            array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_type_id', 'entity_id', 'attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName(array('score/category', 'varchar'), array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'varchar'), array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName(array('score/category', 'varchar'), array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'varchar'), 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'varchar'), 'entity_id', 'score/category', 'entity_id'),
        'entity_id', $installer->getTable('score/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(array('score/category', 'varchar'), 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Category Varchar Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_oggetto'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_oggetto'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Category ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Position')
/*    ->addIndex($installer->getIdxName('score/category_oggetto', array('category_id')),
        array('category_id'))*/
    ->addIndex($installer->getIdxName('score/category_oggetto', array('oggetto_id')),
        array('oggetto_id'))
    ->addForeignKey($installer->getFkName('score/category_oggetto', 'category_id', 'score/category', 'entity_id'),
        'category_id', $installer->getTable('score/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/category_oggetto', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto To Category Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_oggetto_index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_oggetto_index'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Category ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Position')
    ->addColumn('is_parent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Parent')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Visibility')
    ->addIndex(
        $installer->getIdxName(
            'score/category_oggetto_index',
            array('oggetto_id', 'store_id', 'category_id', 'visibility')
        ),
        array('oggetto_id', 'store_id', 'category_id', 'visibility'))
    ->addIndex(
        $installer->getIdxName(
            'score/category_oggetto_index',
            array('store_id', 'category_id', 'visibility', 'is_parent', 'position')
        ),
        array('store_id', 'category_id', 'visibility', 'is_parent', 'position'))
    ->addForeignKey(
        $installer->getFkName('score/category_oggetto_index', 'category_id', 'score/category', 'entity_id'),
        'category_id', $installer->getTable('score/category'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('score/category_oggetto_index', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('score/category_oggetto_index', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Category Oggetto Index');
$installer->getConnection()->createTable($table);

/**
 * Create table 'catalog/compare_item'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/compare_item'))
    ->addColumn('score_compare_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Compare Item ID')
    ->addColumn('visitor_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Visitor ID')
    ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Customer ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Store ID')
    ->addIndex($installer->getIdxName('score/compare_item', array('customer_id')),
        array('customer_id'))
    ->addIndex($installer->getIdxName('score/compare_item', array('oggetto_id')),
        array('oggetto_id'))
    ->addIndex($installer->getIdxName('score/compare_item', array('visitor_id', 'oggetto_id')),
        array('visitor_id', 'oggetto_id'))
    ->addIndex($installer->getIdxName('score/compare_item', array('customer_id', 'oggetto_id')),
        array('customer_id', 'oggetto_id'))
    ->addIndex($installer->getIdxName('score/compare_item', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('score/compare_item', 'customer_id', 'customer/entity', 'entity_id'),
        'customer_id', $installer->getTable('customer/entity'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/compare_item', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/compare_item', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_SET_NULL, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Compare Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_website'))
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Oggetto ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addIndex($installer->getIdxName('score/oggetto_website', array('website_id')),
        array('website_id'))
    ->addForeignKey($installer->getFkName('score/oggetto_website', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/oggetto_website', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto To Website Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_enabled_index'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_enabled_index'))
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Visibility')
    ->addIndex($installer->getIdxName('score/oggetto_enabled_index', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName('score/oggetto_enabled_index', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('score/oggetto_enabled_index', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Visibility Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_link_type'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_link_type'))
    ->addColumn('link_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Link Type ID')
    ->addColumn('code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Code')
    ->setComment('Catalog Oggetto Link Type Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_link'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_link'))
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Link ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('linked_oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Linked Oggetto ID')
    ->addColumn('link_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Link Type ID')
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_link',
            array('link_type_id', 'oggetto_id', 'linked_oggetto_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('link_type_id', 'oggetto_id', 'linked_oggetto_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('score/oggetto_link', array('oggetto_id')),
        array('oggetto_id'))
    ->addIndex($installer->getIdxName('score/oggetto_link', array('linked_oggetto_id')),
        array('linked_oggetto_id'))
    ->addIndex($installer->getIdxName('score/oggetto_link', array('link_type_id')),
        array('link_type_id'))
    ->addForeignKey(
        $installer->getFkName('score/oggetto_link', 'linked_oggetto_id', 'score/oggetto', 'entity_id'),
        'linked_oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('score/oggetto_link', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('score/oggetto_link', 'link_type_id', 'score/oggetto_link_type', 'link_type_id'),
        'link_type_id', $installer->getTable('score/oggetto_link_type'), 'link_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto To Oggetto Linkage Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_link_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_link_attribute'))
    ->addColumn('oggetto_link_attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Oggetto Link Attribute ID')
    ->addColumn('link_type_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Link Type ID')
    ->addColumn('oggetto_link_attribute_code', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Oggetto Link Attribute Code')
    ->addColumn('data_type', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Data Type')
    ->addIndex($installer->getIdxName('score/oggetto_link_attribute', array('link_type_id')),
        array('link_type_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_link_attribute',
            'link_type_id',
            'score/oggetto_link_type',
            'link_type_id'
        ),
        'link_type_id', $installer->getTable('score/oggetto_link_type'), 'link_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Link Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_link_attribute_decimal'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_link_attribute_decimal'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('oggetto_link_attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Oggetto Link Attribute ID')
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned'  => true,
        ), 'Link ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_link_attribute_decimal', array('oggetto_link_attribute_id')),
        array('oggetto_link_attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_link_attribute_decimal', array('link_id')),
        array('link_id'))
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_link_attribute_decimal',
            array('oggetto_link_attribute_id', 'link_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('oggetto_link_attribute_id', 'link_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_link_attribute_decimal',
            'link_id',
            'score/oggetto_link',
            'link_id'
        ),
        'link_id', $installer->getTable('score/oggetto_link'), 'link_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_link_attribute_decimal',
            'oggetto_link_attribute_id',
            'score/oggetto_link_attribute',
            'oggetto_link_attribute_id'
        ),
        'oggetto_link_attribute_id', $installer->getTable('score/oggetto_link_attribute'),
        'oggetto_link_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Link Decimal Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_link_attribute_int'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_link_attribute_int'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('oggetto_link_attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        ), 'Oggetto Link Attribute ID')
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned'  => true,
        ), 'Link ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_link_attribute_int', array('oggetto_link_attribute_id')),
        array('oggetto_link_attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_link_attribute_int', array('link_id')),
        array('link_id'))
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_link_attribute_int',
            array('oggetto_link_attribute_id', 'link_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('oggetto_link_attribute_id', 'link_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_link_attribute_int',
            'link_id',
            'score/oggetto_link',
            'link_id'
        ),
        'link_id', $installer->getTable('score/oggetto_link'), 'link_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_link_attribute_int',
            'oggetto_link_attribute_id',
            'score/oggetto_link_attribute',
            'oggetto_link_attribute_id'
        ),
        'oggetto_link_attribute_id', $installer->getTable('score/oggetto_link_attribute'),
        'oggetto_link_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Link Integer Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_link_attribute_varchar'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_link_attribute_varchar'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('oggetto_link_attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto Link Attribute ID')
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'unsigned'  => true,
        ), 'Link ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_link_attribute_varchar', array('oggetto_link_attribute_id')),
        array('oggetto_link_attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_link_attribute_varchar', array('link_id')),
        array('link_id'))
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_link_attribute_varchar',
            array('oggetto_link_attribute_id', 'link_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('oggetto_link_attribute_id', 'link_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_link_attribute_varchar',
            'link_id',
            'score/oggetto_link',
            'link_id'
        ),
        'link_id', $installer->getTable('score/oggetto_link'), 'link_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_link_attribute_varchar',
            'oggetto_link_attribute_id',
            'score/oggetto_link_attribute',
            'oggetto_link_attribute_id'
        ),
        'oggetto_link_attribute_id', $installer->getTable('score/oggetto_link_attribute'),
        'oggetto_link_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Link Varchar Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_super_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_super_attribute'))
    ->addColumn('oggetto_super_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Oggetto Super Attribute ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Position')
    ->addIndex($installer->getIdxName('score/oggetto_super_attribute', array('oggetto_id')),
        array('oggetto_id'))
    ->addForeignKey(
        $installer->getFkName('score/oggetto_super_attribute', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->setComment('Catalog Oggetto Super Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_super_attribute_label'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_super_attribute_label'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('oggetto_super_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto Super Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('use_default', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Use Default Value')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Value')
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_super_attribute_label',
            array('oggetto_super_attribute_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('oggetto_super_attribute_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('score/oggetto_super_attribute_label', array('oggetto_super_attribute_id')),
        array('oggetto_super_attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_super_attribute_label', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_super_attribute_label',
            'oggetto_super_attribute_id',
            'score/oggetto_super_attribute',
            'oggetto_super_attribute_id'
        ),
        'oggetto_super_attribute_id', $installer->getTable('score/oggetto_super_attribute'),
        'oggetto_super_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName('score/oggetto_super_attribute_label', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Super Attribute Label Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_super_attribute_pricing'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_super_attribute_pricing'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('oggetto_super_attribute_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto Super Attribute ID')
    ->addColumn('value_index', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Value Index')
    ->addColumn('is_percent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Is Percent')
    ->addColumn('pricing_value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Pricing Value')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Website ID')
    ->addIndex($installer->getIdxName('score/oggetto_super_attribute_pricing', array('oggetto_super_attribute_id')),
        array('oggetto_super_attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_super_attribute_pricing', array('website_id')),
        array('website_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_super_attribute_pricing',
            'website_id',
            'core/website',
            'website_id'
        ),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_super_attribute_pricing',
            'oggetto_super_attribute_id',
            'score/oggetto_super_attribute',
            'oggetto_super_attribute_id'
        ),
        'oggetto_super_attribute_id',
        $installer->getTable('score/oggetto_super_attribute'),
        'oggetto_super_attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Super Attribute Pricing Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_super_link'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_super_link'))
    ->addColumn('link_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Link ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Parent ID')
    ->addIndex($installer->getIdxName('score/oggetto_super_link', array('parent_id')),
        array('parent_id'))
    ->addIndex($installer->getIdxName('score/oggetto_super_link', array('oggetto_id')),
        array('oggetto_id'))
    ->addForeignKey($installer->getFkName('score/oggetto_super_link', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/oggetto_super_link', 'parent_id', 'score/oggetto', 'entity_id'),
        'parent_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Super Link Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_attribute_tier_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_attribute_tier_price'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('all_groups', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Applicable To All Customer Groups')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Customer Group ID')
    ->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '1.0000',
        ), 'QTY')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Value')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Website ID')
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_attribute_tier_price',
            array('entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_id', 'all_groups', 'customer_group_id', 'qty', 'website_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('score/oggetto_attribute_tier_price', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('score/oggetto_attribute_tier_price', array('customer_group_id')),
        array('customer_group_id'))
    ->addIndex($installer->getIdxName('score/oggetto_attribute_tier_price', array('website_id')),
        array('website_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_attribute_tier_price',
            'customer_group_id',
            'customer/customer_group',
            'customer_group_id'
        ),
        'customer_group_id', $installer->getTable('customer/customer_group'), 'customer_group_id',
         Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_attribute_tier_price',
            'entity_id',
            'score/oggetto',
            'entity_id'
        ),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_attribute_tier_price',
            'website_id',
            'core/website',
            'website_id'
        ),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Tier Price Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_attribute_media_gallery'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_attribute_media_gallery'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Attribute ID')
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Entity ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_attribute_media_gallery', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_attribute_media_gallery', array('entity_id')),
        array('entity_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_attribute_media_gallery',
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_attribute_media_gallery',
            'entity_id',
            'score/oggetto',
            'entity_id'
        ),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Media Gallery Attribute Backend Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_attribute_media_gallery_value'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_attribute_media_gallery_value'))
    ->addColumn('value_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Value ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('label', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Label')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Position')
    ->addColumn('disabled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Disabled')
    ->addIndex($installer->getIdxName('score/oggetto_attribute_media_gallery_value', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_attribute_media_gallery_value',
            'value_id',
            'score/oggetto_attribute_media_gallery',
            'value_id'
        ),
        'value_id', $installer->getTable('score/oggetto_attribute_media_gallery'), 'value_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_attribute_media_gallery_value',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Media Gallery Attribute Value Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_option'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_option'))
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Option ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('type', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Type')
    ->addColumn('is_require', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Required')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        ), 'SKU')
    ->addColumn('max_characters', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Max Characters')
    ->addColumn('file_extension', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        ), 'File Extension')
    ->addColumn('image_size_x', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        ), 'Image Size X')
    ->addColumn('image_size_y', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned' => true,
        ), 'Image Size Y')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('score/oggetto_option', array('oggetto_id')),
        array('oggetto_id'))
    ->addForeignKey($installer->getFkName('score/oggetto_option', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Option Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_option_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_option_price'))
    ->addColumn('option_price_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Option Price ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Option ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Price')
    ->addColumn('price_type', Varien_Db_Ddl_Table::TYPE_TEXT, 7, array(
        'nullable'  => false,
        'default'   => 'fixed',
        ), 'Price Type')
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_option_price',
            array('option_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('option_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('score/oggetto_option_price', array('option_id')),
        array('option_id'))
    ->addIndex($installer->getIdxName('score/oggetto_option_price', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_option_price',
            'option_id',
            'score/oggetto_option',
            'option_id'
        ),
        'option_id', $installer->getTable('score/oggetto_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_option_price',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Option Price Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_option_title'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_option_title'))
    ->addColumn('option_title_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Option Title ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Option ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Title')
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_option_title',
            array('option_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('option_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('score/oggetto_option_title', array('option_id')),
        array('option_id'))
    ->addIndex($installer->getIdxName('score/oggetto_option_title', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_option_title',
            'option_id',
            'score/oggetto_option',
            'option_id'
        ),
        'option_id', $installer->getTable('score/oggetto_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_option_title',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Option Title Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_option_type_value'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_option_type_value'))
    ->addColumn('option_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Option Type ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Option ID')
    ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
        ), 'SKU')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort Order')
    ->addIndex($installer->getIdxName('score/oggetto_option_type_value', array('option_id')),
        array('option_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_option_type_value',
            'option_id',
            'score/oggetto_option',
            'option_id'
        ),
        'option_id', $installer->getTable('score/oggetto_option'), 'option_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Option Type Value Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_option_type_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_option_type_price'))
    ->addColumn('option_type_price_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Option Type Price ID')
    ->addColumn('option_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Option Type ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'default'   => '0.0000',
        ), 'Price')
    ->addColumn('price_type', Varien_Db_Ddl_Table::TYPE_TEXT, 7, array(
        'nullable'  => false,
        'default'   => 'fixed',
        ), 'Price Type')
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_option_type_price',
            array('option_type_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('option_type_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('score/oggetto_option_type_price', array('option_type_id')),
        array('option_type_id'))
    ->addIndex($installer->getIdxName('score/oggetto_option_type_price', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_option_type_price',
            'option_type_id',
            'score/oggetto_option_type_value',
            'option_type_id'
        ),
        'option_type_id', $installer->getTable('score/oggetto_option_type_value'), 'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_option_type_price',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Option Type Price Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_option_type_title'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_option_type_title'))
    ->addColumn('option_type_title_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Option Type Title ID')
    ->addColumn('option_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Option Type ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Title')
    ->addIndex(
        $installer->getIdxName(
            'score/oggetto_option_type_title',
            array('option_type_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('option_type_id', 'store_id'), array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex($installer->getIdxName('score/oggetto_option_type_title', array('option_type_id')),
        array('option_type_id'))
    ->addIndex($installer->getIdxName('score/oggetto_option_type_title', array('store_id')),
        array('store_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_option_type_title',
            'option_type_id',
            'score/oggetto_option_type_value',
            'option_type_id'
        ),
        'option_type_id', $installer->getTable('score/oggetto_option_type_value'), 'option_type_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/oggetto_option_type_title', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Option Type Title Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/eav_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/eav_attribute'))
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute ID')
    ->addColumn('frontend_input_renderer', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Frontend Input Renderer')
    ->addColumn('is_global', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Global')
    ->addColumn('is_visible', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Visible')
    ->addColumn('is_searchable', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Searchable')
    ->addColumn('is_filterable', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Filterable')
    ->addColumn('is_comparable', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Comparable')
    ->addColumn('is_visible_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Visible On Front')
    ->addColumn('is_html_allowed_on_front', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is HTML Allowed On Front')
    ->addColumn('is_used_for_price_rules', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Used For Price Rules')
    ->addColumn('is_filterable_in_search', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Filterable In Search')
    ->addColumn('used_in_oggetto_listing', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Used In Oggetto Listing')
    ->addColumn('used_for_sort_by', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Used For Sorting')
    ->addColumn('is_configurable', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '1',
        ), 'Is Configurable')
    ->addColumn('apply_to', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        ), 'Apply To')
    ->addColumn('is_visible_in_advanced_search', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Visible In Advanced Search')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Position')
    ->addColumn('is_wysiwyg_enabled', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is WYSIWYG Enabled')
    ->addColumn('is_used_for_promo_rules', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Used For Promo Rules')
    ->addIndex($installer->getIdxName('score/eav_attribute', array('used_for_sort_by')),
        array('used_for_sort_by'))
    ->addIndex($installer->getIdxName('score/eav_attribute', array('used_in_oggetto_listing')),
        array('used_in_oggetto_listing'))
    ->addForeignKey($installer->getFkName('score/eav_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog EAV Attribute Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_relation'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_relation'))
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Parent ID')
    ->addColumn('child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Child ID')
    ->addIndex($installer->getIdxName('score/oggetto_relation', array('child_id')),
        array('child_id'))
    ->addForeignKey($installer->getFkName('score/oggetto_relation', 'child_id', 'score/oggetto', 'entity_id'),
        'child_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/oggetto_relation', 'parent_id', 'score/oggetto', 'entity_id'),
        'parent_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Relation Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_index_eav'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_index_eav'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_index_eav', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('score/oggetto_index_eav', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_index_eav', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('score/oggetto_index_eav', array('value')),
        array('value'))
    ->addForeignKey(
        $installer->getFkName('score/oggetto_index_eav', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/oggetto_index_eav', 'entity_id', 'score/oggetto', 'entity_id'),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('score/oggetto_index_eav', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto EAV Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_index_eav_decimal'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_index_eav_decimal'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_index_eav_decimal', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('score/oggetto_index_eav_decimal', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_index_eav_decimal', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('score/oggetto_index_eav_decimal', array('value')),
        array('value'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_index_eav_decimal',
            'attribute_id',
            'eav/attribute',
            'attribute_id'
        ),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_index_eav_decimal',
            'entity_id',
            'score/oggetto',
            'entity_id'
        ),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_index_eav_decimal',
            'store_id',
            'core/store',
            'store_id'
        ),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto EAV Decimal Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_index_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_index_price'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Tax Class ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Price')
    ->addColumn('final_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Final Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->addIndex($installer->getIdxName('score/oggetto_index_price', array('customer_group_id')),
        array('customer_group_id'))
    ->addIndex($installer->getIdxName('score/oggetto_index_price', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('score/oggetto_index_price', array('min_price')),
        array('min_price'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_index_price',
            'customer_group_id',
            'customer/customer_group',
            'customer_group_id'
        ),
        'customer_group_id', $installer->getTable('customer/customer_group'), 'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_index_price',
            'entity_id',
            'score/oggetto',
            'entity_id'
        ),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_index_price',
            'website_id',
            'core/website',
            'website_id'
        ),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Price Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_index_tier_price'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_index_tier_price'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addIndex($installer->getIdxName('score/oggetto_index_tier_price', array('customer_group_id')),
        array('customer_group_id'))
    ->addIndex($installer->getIdxName('score/oggetto_index_tier_price', array('website_id')),
        array('website_id'))
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_index_tier_price',
            'customer_group_id',
            'customer/customer_group',
            'customer_group_id'
        ),
        'customer_group_id', $installer->getTable('customer/customer_group'), 'customer_group_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_index_tier_price',
            'entity_id',
            'score/oggetto',
            'entity_id'
        ),
        'entity_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey(
        $installer->getFkName(
            'score/oggetto_index_tier_price',
            'website_id',
            'core/website',
            'website_id'
         ),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Tier Price Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_index_website'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_index_website'))
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('website_date', Varien_Db_Ddl_Table::TYPE_DATE, null, array(
        ), 'Website Date')
    ->addColumn('rate', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
        'default'   => '1.0000',
        ), 'Rate')
    ->addIndex($installer->getIdxName('score/oggetto_index_website', array('website_date')),
        array('website_date'))
    ->addForeignKey(
        $installer->getFkName('score/oggetto_index_website', 'website_id', 'core/website', 'website_id'),
        'website_id', $installer->getTable('core/website'), 'website_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog Oggetto Website Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_cfg_option_aggregate_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_cfg_option_aggregate_idx'))
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Parent ID')
    ->addColumn('child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Child ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->setComment('Catalog Oggetto Price Indexer Config Option Aggregate Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_cfg_option_aggregate_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_cfg_option_aggregate_tmp'))
    ->addColumn('parent_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Parent ID')
    ->addColumn('child_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Child ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->setComment('Catalog Oggetto Price Indexer Config Option Aggregate Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_cfg_option_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_cfg_option_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->setComment('Catalog Oggetto Price Indexer Config Option Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_cfg_option_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_cfg_option_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->setComment('Catalog Oggetto Price Indexer Config Option Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_final_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_final_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Tax Class ID')
    ->addColumn('orig_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Original Price')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->addColumn('base_tier', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Base Tier')
    ->setComment('Catalog Oggetto Price Indexer Final Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_final_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_final_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Tax Class ID')
    ->addColumn('orig_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Original Price')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->addColumn('base_tier', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Base Tier')
    ->setComment('Catalog Oggetto Price Indexer Final Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_option_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_option_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->setComment('Catalog Oggetto Price Indexer Option Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_option_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_option_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->setComment('Catalog Oggetto Price Indexer Option Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_option_aggregate_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_option_aggregate_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Option ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->setComment('Catalog Oggetto Price Indexer Option Aggregate Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_option_aggregate_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_option_aggregate_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('option_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'default'   => '0',
        ), 'Option ID')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->setComment('Catalog Oggetto Price Indexer Option Aggregate Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_eav_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_eav_indexer_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_eav_indexer_idx', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_indexer_idx', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_indexer_idx', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_indexer_idx', array('value')),
        array('value'))
    ->setComment('Catalog Oggetto EAV Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_eav_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_eav_indexer_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_eav_indexer_tmp', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_indexer_tmp', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_indexer_tmp', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_indexer_tmp', array('value')),
        array('value'))
    ->setComment('Catalog Oggetto EAV Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_eav_decimal_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_eav_decimal_indexer_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_eav_decimal_indexer_idx', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_decimal_indexer_idx', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_decimal_indexer_idx', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_decimal_indexer_idx', array('value')),
        array('value'))
    ->setComment('Catalog Oggetto EAV Decimal Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_eav_decimal_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_eav_decimal_indexer_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Attribute ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Store ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        'nullable'  => false,
        'primary'   => true,
        ), 'Value')
    ->addIndex($installer->getIdxName('score/oggetto_eav_decimal_indexer_tmp', array('entity_id')),
        array('entity_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_decimal_indexer_tmp', array('attribute_id')),
        array('attribute_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_decimal_indexer_tmp', array('store_id')),
        array('store_id'))
    ->addIndex($installer->getIdxName('score/oggetto_eav_decimal_indexer_tmp', array('value')),
        array('value'))
    ->setComment('Catalog Oggetto EAV Decimal Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_idx'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Tax Class ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Price')
    ->addColumn('final_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Final Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->addIndex($installer->getIdxName('score/oggetto_price_indexer_idx', array('customer_group_id')),
        array('customer_group_id'))
    ->addIndex($installer->getIdxName('score/oggetto_price_indexer_idx', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('score/oggetto_price_indexer_idx', array('min_price')),
        array('min_price'))
    ->setComment('Catalog Oggetto Price Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/oggetto_price_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/oggetto_price_indexer_tmp'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('customer_group_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Customer Group ID')
    ->addColumn('website_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Website ID')
    ->addColumn('tax_class_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'default'   => '0',
        ), 'Tax Class ID')
    ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Price')
    ->addColumn('final_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Final Price')
    ->addColumn('min_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Min Price')
    ->addColumn('max_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Max Price')
    ->addColumn('tier_price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
        ), 'Tier Price')
    ->addIndex($installer->getIdxName('score/oggetto_price_indexer_tmp', array('customer_group_id')),
        array('customer_group_id'))
    ->addIndex($installer->getIdxName('score/oggetto_price_indexer_tmp', array('website_id')),
        array('website_id'))
    ->addIndex($installer->getIdxName('score/oggetto_price_indexer_tmp', array('min_price')),
        array('min_price'))
    ->setComment('Catalog Oggetto Price Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_oggetto_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_oggetto_indexer_idx'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Category ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Position')
    ->addColumn('is_parent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Parent')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Visibility')
    ->addIndex(
        $installer->getIdxName(
            'score/category_oggetto_indexer_idx',
            array('oggetto_id', 'category_id', 'store_id')
        ),
        array('oggetto_id', 'category_id', 'store_id'))
    ->setComment('Catalog Category Oggetto Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_oggetto_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_oggetto_indexer_tmp'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Category ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'nullable'  => false,
        'default'   => '0',
        ), 'Position')
    ->addColumn('is_parent', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Is Parent')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Visibility')
    ->setComment('Catalog Category Oggetto Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_oggetto_enabled_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_oggetto_enabled_indexer_idx'))
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Visibility')
    ->addIndex($installer->getIdxName('score/category_oggetto_enabled_indexer_idx', array('oggetto_id')),
        array('oggetto_id'))
    ->setComment('Catalog Category Oggetto Enabled Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_oggetto_enabled_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_oggetto_enabled_indexer_tmp'))
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Visibility')
    ->addIndex($installer->getIdxName('score/category_oggetto_enabled_indexer_tmp', array('oggetto_id')),
        array('oggetto_id'))
    ->setComment('Catalog Category Oggetto Enabled Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_anchor_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_anchor_indexer_idx'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Category ID')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Path')
    ->addIndex($installer->getIdxName('score/category_anchor_indexer_idx', array('category_id')),
        array('category_id'))
    ->setComment('Catalog Category Anchor Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_anchor_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_anchor_indexer_tmp'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Category ID')
    ->addColumn('path', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Path')
    ->addIndex($installer->getIdxName('score/category_anchor_indexer_tmp', array('category_id')),
        array('category_id'))
    ->setComment('Catalog Category Anchor Indexer Temp Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_anchor_oggettos_indexer_idx'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_anchor_oggettos_indexer_idx'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Category ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->addColumn('position', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        ), 'Position')
    ->setComment('Catalog Category Anchor Oggetto Indexer Index Table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'score/category_anchor_oggettos_indexer_tmp'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('score/category_anchor_oggettos_indexer_tmp'))
    ->addColumn('category_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Category ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Oggetto ID')
    ->setComment('Catalog Category Anchor Oggetto Indexer Temp Table');
$installer->getConnection()->createTable($table);


/**
 * Modify core/url_rewrite table
 *
 */
$installer->getConnection()->addColumn($installer->getTable('core/url_rewrite'), 'category_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned'  => true,
    'nullable'  => true,
    'comment'   => 'Category Id'
));
$installer->getConnection()->addColumn($installer->getTable('core/url_rewrite'), 'oggetto_id', array(
    'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
    'unsigned'  => true,
    'nullable'  => true,
    'comment'   => 'Oggetto Id'
));
$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'category_id', 'score/category', 'entity_id'),
    $installer->getTable('core/url_rewrite'), 'category_id',
    $installer->getTable('score/category'), 'entity_id');
$installer->getConnection()->addForeignKey(
    $installer->getFkName('core/url_rewrite', 'oggetto_id', 'score/category', 'entity_id'),
    $installer->getTable('core/url_rewrite'), 'oggetto_id',
    $installer->getTable('score/oggetto'), 'entity_id');

$installer->endSetup();

$installer->installEntities();
