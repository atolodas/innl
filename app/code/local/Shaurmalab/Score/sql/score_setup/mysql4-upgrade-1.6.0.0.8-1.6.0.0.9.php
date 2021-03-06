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

$installFile = dirname(__FILE__) . DS . 'upgrade-1.6.0.0.8-1.6.0.0.9.php';
if (file_exists($installFile)) {
    include $installFile;
}

/** @var $installer Shaurmalab_Score_Model_Resource_Setup */
$installer = $this;
/** @var $connection Varien_Db_Adapter_Pdo_Mysql */
$connection = $installer->getConnection();
$memoryTables = array(
    'score/category_anchor_indexer_tmp',
    'score/category_anchor_oggettos_indexer_tmp',
    'score/category_oggetto_enabled_indexer_tmp',
    'score/category_oggetto_indexer_tmp',
    'score/oggetto_eav_decimal_indexer_tmp',
    'score/oggetto_eav_indexer_tmp',
    'score/oggetto_price_indexer_cfg_option_aggregate_tmp',
    'score/oggetto_price_indexer_cfg_option_tmp',
    'score/oggetto_price_indexer_final_tmp',
    'score/oggetto_price_indexer_option_aggregate_tmp',
    'score/oggetto_price_indexer_option_tmp',
    'score/oggetto_price_indexer_tmp',
);

foreach ($memoryTables as $table) {
    $connection->changeTableEngine($installer->getTable($table), Varien_Db_Adapter_Pdo_Mysql::ENGINE_MEMORY);
}
