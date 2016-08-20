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
 * Score oggetto website resource model
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Status extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Oggetto atrribute cache
     *
     * @var array
     */
    protected $_oggettoAttributes  = array();

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_enabled_index', 'oggetto_id');
    }

    /**
     * Retrieve oggetto attribute (public method for status model)
     *
     * @param string $attributeCode
     * @return Shaurmalab_Score_Model_Resource_Eav_Attribute
     */
    public function getOggettoAttribute($attributeCode)
    {
        return $this->_getOggettoAttribute($attributeCode);
    }

    /**
     * Retrieve oggetto attribute
     *
     * @param unknown_type $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    protected function _getOggettoAttribute($attribute)
    {
        if (empty($this->_oggettoAttributes[$attribute])) {
            $this->_oggettoAttributes[$attribute] = Mage::getSingleton('score/oggetto')->getResource()->getAttribute($attribute);
        }
        return $this->_oggettoAttributes[$attribute];
    }

    /**
     * Refresh enabled index cache
     *
     * @param int $oggettoId
     * @param int $storeId
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Status
     */
    public function refreshEnabledIndex($oggettoId, $storeId)
    {
        if ($storeId == Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID) {
            foreach (Mage::app()->getStores() as $store) {
                $this->refreshEnabledIndex($oggettoId, $store->getId());
            }

            return $this;
        }

        Mage::getResourceSingleton('score/oggetto')->refreshEnabledIndex($storeId, $oggettoId);

        return $this;
    }

    /**
     * Update oggetto status for store
     *
     * @param int $oggettoId
     * @param int $storId
     * @param int $value
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Status
     */
    public function updateOggettoStatus($oggettoId, $storeId, $value)
    {
        $statusAttributeId  = $this->_getOggettoAttribute('status')->getId();
        $statusEntityTypeId = $this->_getOggettoAttribute('status')->getEntityTypeId();
        $statusTable        = $this->_getOggettoAttribute('status')->getBackend()->getTable();
        $refreshIndex       = true;
        $adapter            = $this->_getWriteAdapter();

        $data = new Varien_Object(array(
            'entity_type_id' => $statusEntityTypeId,
            'attribute_id'   => $statusAttributeId,
            'store_id'       => $storeId,
            'entity_id'      => $oggettoId,
            'value'          => $value
        ));

        $data = $this->_prepareDataForTable($data, $statusTable);

        $select = $adapter->select()
            ->from($statusTable)
            ->where('attribute_id = :attribute_id')
            ->where('store_id     = :store_id')
            ->where('entity_id    = :oggetto_id');

        $binds = array(
            'attribute_id' => $statusAttributeId,
            'store_id'     => $storeId,
            'oggetto_id'   => $oggettoId
        );

        $row = $adapter->fetchRow($select);

        if ($row) {
            if ($row['value'] == $value) {
                $refreshIndex = false;
            } else {
                $condition = array('value_id = ?' => $row['value_id']);
                $adapter->update($statusTable, $data, $condition);
            }
        } else {
            $adapter->insert($statusTable, $data);
        }

        if ($refreshIndex) {
            $this->refreshEnabledIndex($oggettoId, $storeId);
        }

        return $this;
    }

    /**
     * Retrieve Oggetto(s) status for store
     * Return array where key is a oggetto_id, value - status
     *
     * @param array|int $oggettoIds
     * @param int $storeId
     * @return array
     */
    public function getOggettoStatus($oggettoIds, $storeId = null)
    {
        $statuses = array();

        $attribute      = $this->_getOggettoAttribute('status');
        $attributeTable = $attribute->getBackend()->getTable();
        $adapter        = $this->_getReadAdapter();

        if (!is_array($oggettoIds)) {
            $oggettoIds = array($oggettoIds);
        }

        if ($storeId === null || $storeId == Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID) {
            $select = $adapter->select()
                ->from($attributeTable, array('entity_id', 'value'))
                ->where('entity_id IN (?)', $oggettoIds)
                ->where('attribute_id = ?', $attribute->getAttributeId())
                ->where('store_id = ?', Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID);

            $rows = $adapter->fetchPairs($select);
        } else {
            $valueCheckSql = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');

            $select = $adapter->select()
                ->from(
                    array('t1' => $attributeTable),
                    array('value' => $valueCheckSql))
                ->joinLeft(
                    array('t2' => $attributeTable),
                    't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id = ' . (int)$storeId,
                    array('t1.entity_id')
                )
                ->where('t1.store_id = ?', Mage_Core_Model_App::ADMIN_STORE_ID)
                ->where('t1.attribute_id = ?', $attribute->getAttributeId())
                ->where('t1.entity_id IN(?)', $oggettoIds);
            $rows = $adapter->fetchPairs($select);
        }

        foreach ($oggettoIds as $oggettoId) {
            if (isset($rows[$oggettoId])) {
                $statuses[$oggettoId] = $rows[$oggettoId];
            } else {
                $statuses[$oggettoId] = -1;
            }
        }

        return $statuses;
    }
}
