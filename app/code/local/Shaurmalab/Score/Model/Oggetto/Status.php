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
 * Oggetto status functionality model
 *
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Status _getResource()
 * @method Shaurmalab_Score_Model_Resource_Oggetto_Status getResource()
 * @method int getOggettoId()
 * @method Shaurmalab_Score_Model_Oggetto_Status setOggettoId(int $value)
 * @method int getStoreId()
 * @method Shaurmalab_Score_Model_Oggetto_Status setStoreId(int $value)
 * @method int getVisibility()
 * @method Shaurmalab_Score_Model_Oggetto_Status setVisibility(int $value)
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Status extends Mage_Core_Model_Abstract
{
    const STATUS_ENABLED    = 1;
    const STATUS_DISABLED   = 2;

    /**
     * Reference to the attribute instance
     *
     * @var Shaurmalab_Score_Model_Resource_Eav_Attribute
     */
    protected $_attribute;

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_status');
    }

    /**
     * Retrieve resource model wrapper
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Status
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve Oggetto Attribute by code
     *
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute_Abstract
     */
    public function getOggettoAttribute($attributeCode)
    {
        return $this->_getResource()->getOggettoAttribute($attributeCode);
    }

    /**
     * Add visible filter to Oggetto Collection
     *
     * @deprecated remove on new builds
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Shaurmalab_Score_Model_Oggetto_Status
     */
    public function addVisibleFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        //$collection->addAttributeToFilter('status', array('in'=>$this->getVisibleStatusIds()));
        return $this;
    }

    /**
     * Add saleable filter to Oggetto Collection
     *
     * @deprecated remove on new builds
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @return Shaurmalab_Score_Model_Oggetto_Status
     */
    public function addSaleableFilterToCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        //$collection->addAttributeToFilter('status', array('in'=>$this->getSaleableStatusIds()));
        return $this;
    }

    /**
     * Retrieve Visible Status Ids
     *
     * @return array
     */
    public function getVisibleStatusIds()
    {
        return array(self::STATUS_ENABLED);
    }

    /**
     * Retrieve Saleable Status Ids
     * Default Oggetto Enable status
     *
     * @return array
     */
    public function getSaleableStatusIds()
    {
        return array(self::STATUS_ENABLED);
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('score')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('score')->__('Disabled')
        );
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    static public function getAllOption()
    {
        $options = self::getOptionArray();
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    static public function getAllOptions()
    {
        $res = array(
            array(
                'value' => '',
                'label' => Mage::helper('score')->__('-- Please Select --')
            )
        );
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    static public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * Update status value for oggetto
     *
     * @param   int $oggettoId
     * @param   int $storeId
     * @param   int $value
     * @return  Shaurmalab_Score_Model_Oggetto_Status
     */
    public function updateOggettoStatus($oggettoId, $storeId, $value)
    {
        Mage::getSingleton('score/oggetto_action')
            ->updateAttributes(array($oggettoId), array('status' => $value), $storeId);

        // add back compatibility event
        $status = $this->_getResource()->getOggettoAttribute('status');
        if ($status->isScopeWebsite()) {
            $website = Mage::app()->getStore($storeId)->getWebsite();
            $stores  = $website->getStoreIds();
        } else if ($status->isScopeStore()) {
            $stores = array($storeId);
        } else {
            $stores = array_keys(Mage::app()->getStores());
        }

        foreach ($stores as $storeId) {
            Mage::dispatchEvent('score_oggetto_status_update', array(
                'oggetto_id'    => $oggettoId,
                'store_id'      => $storeId,
                'status'        => $value
            ));
        }

        return $this;
    }

    /**
     * Retrieve Oggetto(s) status for store
     * Return array where key is oggetto, value - status
     *
     * @param int|array $oggettoIds
     * @param int $storeId
     * @return array
     */
    public function getOggettoStatus($oggettoIds, $storeId = null)
    {
        return $this->getResource()->getOggettoStatus($oggettoIds, $storeId);
    }

    /**
     * ---------------- Eav Source methods for Flat data -----------------------
     */

    /**
     * Retrieve flat column definition
     *
     * @return array
     */
    public function getFlatColums()
    {
        return array();
    }

    /**
     * Retrieve Indexes for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        return array();
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return null;
    }

    /**
     * Set attribute instance
     *
     * @param Shaurmalab_Score_Model_Resource_Eav_Attribute $attribute
     * @return Mage_Eav_Model_Entity_Attribute_Frontend_Abstract
     */
    public function setAttribute($attribute)
    {
        $this->_attribute = $attribute;
        return $this;
    }

    /**
     * Get attribute instance
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Attribute
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Add Value Sort To Collection Select
     *
     * @param Mage_Eav_Model_Entity_Collection_Abstract $collection
     * @param string $dir direction
     * @return Mage_Eav_Model_Entity_Attribute_Source_Abstract
     */
    public function addValueSortToCollection($collection, $dir = 'asc')
    {
        $attributeCode  = $this->getAttribute()->getAttributeCode();
        $attributeId    = $this->getAttribute()->getId();
        $attributeTable = $this->getAttribute()->getBackend()->getTable();

        if ($this->getAttribute()->isScopeGlobal()) {
            $tableName = $attributeCode . '_t';
            $collection->getSelect()
                ->joinLeft(
                    array($tableName => $attributeTable),
                    "e.entity_id={$tableName}.entity_id"
                        . " AND {$tableName}.attribute_id='{$attributeId}'"
                        . " AND {$tableName}.store_id='0'",
                    array());
            $valueExpr = $tableName . '.value';
        }
        else {
            $valueTable1 = $attributeCode . '_t1';
            $valueTable2 = $attributeCode . '_t2';
            $collection->getSelect()
                ->joinLeft(
                    array($valueTable1 => $attributeTable),
                    "e.entity_id={$valueTable1}.entity_id"
                        . " AND {$valueTable1}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable1}.store_id='0'",
                    array())
                ->joinLeft(
                    array($valueTable2 => $attributeTable),
                    "e.entity_id={$valueTable2}.entity_id"
                        . " AND {$valueTable2}.attribute_id='{$attributeId}'"
                        . " AND {$valueTable2}.store_id='{$collection->getStoreId()}'",
                    array()
                );

                $valueExpr = $collection->getConnection()->getCheckSql(
                    $valueTable2 . '.value_id > 0',
                    $valueTable2 . '.value',
                    $valueTable1 . '.value'
                );
        }

        $collection->getSelect()->order($valueExpr . ' ' . $dir);
        return $this;
    }
}
