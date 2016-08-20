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
 * @package     Shaurmalab_ScoreSearch
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();

/**
 * Create table 'scoresearch/search_query'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('scoresearch/search_query'))
    ->addColumn('query_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Query ID')
    ->addColumn('query_text', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Query text')
    ->addColumn('num_results', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Num results')
    ->addColumn('popularity', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Popularity')
    ->addColumn('redirect', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Redirect')
    ->addColumn('synonym_for', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
        ), 'Synonym for')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Store ID')
    ->addColumn('display_in_terms', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'nullable'  => false,
        'default'   => '1',
        ), 'Display in terms')
    ->addColumn('is_active', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'default'   => '1',
        ), 'Active status')
    ->addColumn('is_processed', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'default'   => '0',
        ), 'Processed status')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
        'nullable'  => false,
        ), 'Updated at')
    ->addIndex($installer->getIdxName('scoresearch/search_query', array('query_text','store_id','popularity')),
        array('query_text','store_id','popularity'))
    ->addIndex($installer->getIdxName('scoresearch/search_query', 'store_id'), 'store_id')
    ->addForeignKey($installer->getFkName('scoresearch/search_query', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog search query table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'scoresearch/result'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('scoresearch/result'))
    ->addColumn('query_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Query ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Oggetto ID')
    ->addColumn('relevance', Varien_Db_Ddl_Table::TYPE_DECIMAL, '20,4', array(
        'nullable'  => false,
        'default'   => '0.0000'
        ), 'Relevance')
    ->addIndex($installer->getIdxName('scoresearch/result', 'query_id'), 'query_id')
    ->addForeignKey($installer->getFkName('scoresearch/result', 'query_id', 'scoresearch/search_query', 'query_id'),
        'query_id', $installer->getTable('scoresearch/search_query'), 'query_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addIndex($installer->getIdxName('scoresearch/result', 'oggetto_id'), 'oggetto_id')
    ->addForeignKey($installer->getFkName('scoresearch/result', 'oggetto_id', 'score/oggetto', 'entity_id'),
        'oggetto_id', $installer->getTable('score/oggetto'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Catalog search result table');
$installer->getConnection()->createTable($table);

/**
 * Create table 'scoresearch/fulltext'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('scoresearch/fulltext'))
    ->addColumn('fulltext_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'Entity ID')
    ->addColumn('oggetto_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Oggetto ID')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        ), 'Store ID')
    ->addColumn('data_index', Varien_Db_Ddl_Table::TYPE_TEXT, '4g', array(
        ), 'Data index')
    ->addIndex(
        $installer->getIdxName(
            'scoresearch/fulltext',
            array('oggetto_id', 'store_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('oggetto_id', 'store_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->addIndex(
        $installer->getIdxName(
            'scoresearch/fulltext',
            'data_index',
            Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT
         ),
        'data_index',
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT))
    ->setOption('type', 'MyISAM')
    ->setComment('Catalog search result table');
$installer->getConnection()->createTable($table);

$installer->endSetup();
