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

/**
 * Oggetto url rewrite helper
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Helper_Oggetto_Url_Rewrite
    implements Shaurmalab_Score_Helper_Oggetto_Url_Rewrite_Interface
{
    /**
     * Adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     * Resource instance
     *
     * @var Mage_Core_Model_Resource
     */
    protected $_resource;

    /**
     * Initialize resource and connection instances
     *
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        $this->_resource = Mage::getSingleton('core/resource');
        $this->_connection = !empty($args['connection']) ? $args['connection'] : $this->_resource
            ->getConnection(Mage_Core_Model_Resource::DEFAULT_READ_RESOURCE);
    }

    /**
     * Prepare and return select
     *
     * @param array $oggettoIds
     * @param int $categoryId
     * @param int $storeId
     * @return Varien_Db_Select
     */
    public function getTableSelect(array $oggettoIds, $categoryId, $storeId)
    {
        $select = $this->_connection->select()
            ->from($this->_resource->getTableName('core/url_rewrite'), array('oggetto_id', 'request_path'))
            ->where('store_id = ?', (int)$storeId)
            ->where('is_system = ?', 1)
            ->where('category_id = ? OR category_id IS NULL', (int)$categoryId)
            ->where('oggetto_id IN(?)', $oggettoIds)
            ->order('category_id ' . Varien_Data_Collection::SORT_ORDER_DESC);
        return $select;
    }

    /**
     * Prepare url rewrite left join statement for given select instance and store_id parameter.
     *
     * @param Varien_Db_Select $select
     * @param int $storeId
     * @return Shaurmalab_Score_Helper_Oggetto_Url_Rewrite_Interface
     */
    public function joinTableToSelect(Varien_Db_Select $select, $storeId)
    {
        $select->joinLeft(
            array('url_rewrite' => $this->_resource->getTableName('core/url_rewrite')),
            'url_rewrite.oggetto_id = main_table.entity_id AND url_rewrite.is_system = 1 AND ' .
                $this->_connection->quoteInto('url_rewrite.category_id IS NULL AND url_rewrite.store_id = ? AND ',
                    (int)$storeId) .
                $this->_connection->prepareSqlCondition('url_rewrite.id_path', array('like' => 'oggetto/%')),
            array('request_path' => 'url_rewrite.request_path'));
        return $this;
    }
}
