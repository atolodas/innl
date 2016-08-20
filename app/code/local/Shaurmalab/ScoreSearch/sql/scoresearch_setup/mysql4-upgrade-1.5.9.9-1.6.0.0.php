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

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Drop foreign keys
 */
$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoresearch/search_query'),
    'FK_SCORESEARCH_QUERY_STORE'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoresearch/result'),
    'FK_SCORESEARCH_RESULT_CATALOG_PRODUCT'
);

$installer->getConnection()->dropForeignKey(
    $installer->getTable('scoresearch/result'),
    'FK_SCORESEARCH_RESULT_QUERY'
);


/**
 * Drop indexes
 */
$installer->getConnection()->dropIndex(
    $installer->getTable('scoresearch/fulltext'),
    'PRIMARY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoresearch/fulltext'),
    'data_index'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoresearch/search_query'),
    'FK_SCORESEARCH_QUERY_STORE'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoresearch/search_query'),
    'IDX_SEARCH_QUERY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoresearch/result'),
    'IDX_QUERY'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoresearch/result'),
    'IDX_PRODUCT'
);

$installer->getConnection()->dropIndex(
    $installer->getTable('scoresearch/result'),
    'IDX_RELEVANCE'
);


/*
 * Change columns
 */
$tables = array(
    $installer->getTable('scoresearch/search_query') => array(
        'columns' => array(
            'query_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'identity'  => true,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Query ID'
            ),
            'query_text' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Query text'
            ),
            'num_results' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Num results'
            ),
            'popularity' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Popularity'
            ),
            'redirect' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Redirect'
            ),
            'synonym_for' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => 255,
                'comment'   => 'Synonym for'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => '0',
                'comment'   => 'Store ID'
            ),
            'display_in_terms' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'nullable'  => false,
                'default'   => '1',
                'comment'   => 'Display in terms'
            ),
            'is_active' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'default'   => '1',
                'comment'   => 'Active status'
            ),
            'is_processed' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'default'   => '0',
                'comment'   => 'Processed status'
            ),
            'updated_at' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
                'nullable'  => false,
                'comment'   => 'Updated at'
            )
        ),
        'comment' => 'Catalog search query table'
    ),
    $installer->getTable('scoresearch/result') => array(
        'columns' => array(
            'query_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Query ID'
            ),
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'primary'   => true,
                'comment'   => 'Oggetto ID'
            ),
            'relevance' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_DECIMAL,
                'scale'     => 4,
                'precision' => 20,
                'nullable'  => false,
                'default'   => '0.0000',
                'comment'   => 'Relevance'
            )
        ),
        'comment' => 'Catalog search result table'
    ),
    $installer->getTable('scoresearch/fulltext') => array(
        'columns' => array(
            'oggetto_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Oggetto ID'
            ),
            'store_id' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_SMALLINT,
                'unsigned'  => true,
                'nullable'  => false,
                'comment'   => 'Store ID'
            ),
            'data_index' => array(
                'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
                'length'    => '4g',
                'comment'   => 'Data index'
            )
        ),
        'comment' => 'Catalog search result table'
    )
);

$installer->getConnection()->modifyTables($tables);

/**
 * Change columns
 */
$installer->getConnection()->addColumn(
    $installer->getTable('scoresearch/fulltext'),
    'fulltext_id',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        'comment'   => 'Entity ID'
    )
);

/**
 * Add indexes
 */
$installer->getConnection()->addIndex(
    $installer->getTable('scoresearch/fulltext'),
    $installer->getIdxName(
        'scoresearch/fulltext',
        array('oggetto_id', 'store_id'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
    ),
    array('oggetto_id', 'store_id'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoresearch/fulltext'),
    $installer->getIdxName(
        'scoresearch/fulltext',
        array('data_index'),
        Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT
    ),
    array('data_index'),
    Varien_Db_Adapter_Interface::INDEX_TYPE_FULLTEXT
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoresearch/search_query'),
    $installer->getIdxName('scoresearch/search_query', array('query_text', 'store_id', 'popularity')),
    array('query_text', 'store_id', 'popularity')
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoresearch/search_query'),
    $installer->getIdxName('scoresearch/search_query', array('store_id')),
    array('store_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoresearch/result'),
    $installer->getIdxName('scoresearch/result', array('query_id')),
    array('query_id')
);

$installer->getConnection()->addIndex(
    $installer->getTable('scoresearch/result'),
    $installer->getIdxName('scoresearch/result', array('oggetto_id')),
    array('oggetto_id')
);


/**
 * Add foreign keys
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoresearch/search_query', 'store_id', 'core/store', 'store_id'),
    $installer->getTable('scoresearch/search_query'),
    'store_id',
    $installer->getTable('core/store'),
    'store_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoresearch/result', 'query_id', 'scoresearch/search_query', 'query_id'),
    $installer->getTable('scoresearch/result'),
    'query_id',
    $installer->getTable('scoresearch/search_query'),
    'query_id'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('scoresearch/result', 'oggetto_id', 'score/oggetto', 'entity_id'),
    $installer->getTable('scoresearch/result'),
    'oggetto_id',
    $installer->getTable('score/oggetto'),
    'entity_id'
);

$installer->endSetup();
