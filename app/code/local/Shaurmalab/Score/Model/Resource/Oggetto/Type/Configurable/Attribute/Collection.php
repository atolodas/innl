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
 * Score Configurable Oggetto Attribute Collection
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Attribute_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Configurable attributes label table name
     *
     * @var string
     */
    protected $_labelTable;

    /**
     * Configurable attributes price table name
     *
     * @var string
     */
    protected $_priceTable;

    /**
     * Oggetto instance
     *
     * @var Shaurmalab_Score_Model_Oggetto
     */
    protected $_oggetto;

    /**
     * Initialize connection and define table names
     *
     */
    protected function _construct()
    {
        $this->_init('score/oggetto_type_configurable_attribute');
        $this->_labelTable = $this->getTable('score/oggetto_super_attribute_label');
        $this->_priceTable = $this->getTable('score/oggetto_super_attribute_pricing');
    }

    /**
     * Retrieve score helper
     *
     * @return Shaurmalab_Score_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('score');
    }

    /**
     * Set Oggetto filter (Configurable)
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Attribute_Collection
     */
    public function setOggettoFilter($oggetto)
    {
        $this->_oggetto = $oggetto;
        return $this->addFieldToFilter('oggetto_id', $oggetto->getId());
    }

    /**
     * Set order collection by Position
     *
     * @param string $dir
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Attribute_Collection
     */
    public function orderByPosition($dir = self::SORT_ORDER_ASC)
    {
        $this->setOrder('position ',  $dir);
        return $this;
    }

    /**
     * Retrieve Store Id
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int)$this->_oggetto->getStoreId();
    }

    /**
     * After load collection process
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Attribute_Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        Varien_Profiler::start('TTT1:'.__METHOD__);
        $this->_addOggettoAttributes();
        Varien_Profiler::stop('TTT1:'.__METHOD__);
        Varien_Profiler::start('TTT2:'.__METHOD__);
        $this->_addAssociatedOggettoFilters();
        Varien_Profiler::stop('TTT2:'.__METHOD__);
        Varien_Profiler::start('TTT3:'.__METHOD__);
        $this->_loadLabels();
        Varien_Profiler::stop('TTT3:'.__METHOD__);
        Varien_Profiler::start('TTT4:'.__METHOD__);
        $this->_loadPrices();
        Varien_Profiler::stop('TTT4:'.__METHOD__);
        return $this;
    }

    /**
     * Add oggetto attributes to collection items
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Attribute_Collection
     */
    protected function _addOggettoAttributes()
    {
        foreach ($this->_items as $item) {
            $oggettoAttribute = $this->getOggetto()->getTypeInstance(true)
                ->getAttributeById($item->getAttributeId(), $this->getOggetto());
            $item->setOggettoAttribute($oggettoAttribute);
        }
        return $this;
    }

    /**
     * Add Associated Oggetto Filters (From Oggetto Type Instance)
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Attribute_Collection
     */
    public function _addAssociatedOggettoFilters()
    {
        $this->getOggetto()->getTypeInstance(true)
            ->getUsedOggettos($this->getColumnValues('attribute_id'), $this->getOggetto()); // Filter associated oggettos
        return $this;
    }

    /**
     * Load attribute labels
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Attribute_Collection
     */
    protected function _loadLabels()
    {
        if ($this->count()) {
            $useDefaultCheck = $this->getConnection()->getCheckSql(
                'store.use_default IS NULL',
                'def.use_default',
                'store.use_default'
            );

            $labelCheck = $this->getConnection()->getCheckSql(
                'store.value IS NULL',
                'def.value',
                'store.value'
            );

            $select = $this->getConnection()->select()
                ->from(array('def' => $this->_labelTable))
                ->joinLeft(
                    array('store' => $this->_labelTable),
                    $this->getConnection()->quoteInto('store.oggetto_super_attribute_id = def.oggetto_super_attribute_id AND store.store_id = ?', $this->getStoreId()),
                    array(
                        'use_default' => $useDefaultCheck,
                        'label' => $labelCheck
                    ))
                ->where('def.oggetto_super_attribute_id IN (?)', array_keys($this->_items))
                ->where('def.store_id = ?', 0);

                $result = $this->getConnection()->fetchAll($select);
                foreach ($result as $data) {
                    $this->getItemById($data['oggetto_super_attribute_id'])->setLabel($data['label']);
                    $this->getItemById($data['oggetto_super_attribute_id'])->setUseDefault($data['use_default']);
                }
        }
        return $this;
    }

    /**
     * Load attribute prices information
     *
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Attribute_Collection
     */
    protected function _loadPrices()
    {
        if ($this->count()) {
            $pricings = array(
                0 => array()
            );

            if ($this->getHelper()->isPriceGlobal()) {
                $websiteId = 0;
            } else {
                $websiteId = (int)Mage::app()->getStore($this->getStoreId())->getWebsiteId();
                $pricing[$websiteId] = array();
            }

            $select = $this->getConnection()->select()
                ->from(array('price' => $this->_priceTable))
                ->where('price.oggetto_super_attribute_id IN (?)', array_keys($this->_items));

            if ($websiteId > 0) {
                $select->where('price.website_id IN(?)', array(0, $websiteId));
            } else {
                $select->where('price.website_id = ?', 0);
            }

            $query = $this->getConnection()->query($select);

            while ($row = $query->fetch()) {
                $pricings[(int)$row['website_id']][] = $row;
            }

            $values = array();

            foreach ($this->_items as $item) {
               $oggettoAttribute = $item->getOggettoAttribute();
               if (!($oggettoAttribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract)) {
                   continue;
               }
               $options = $oggettoAttribute->getFrontend()->getSelectOptions();
               foreach ($options as $option) {
                   foreach ($this->getOggetto()->getTypeInstance(true)->getUsedOggettos(null, $this->getOggetto()) as $associatedOggetto) {
                        if (!empty($option['value'])
                            && $option['value'] == $associatedOggetto->getData(
                                                        $oggettoAttribute->getAttributeCode())) {
                            // If option available in associated oggetto
                            if (!isset($values[$item->getId() . ':' . $option['value']])) {
                                // If option not added, we will add it.
                                $values[$item->getId() . ':' . $option['value']] = array(
                                    'oggetto_super_attribute_id' => $item->getId(),
                                    'value_index'                => $option['value'],
                                    'label'                      => $option['label'],
                                    'default_label'              => $option['label'],
                                    'store_label'                => $option['label'],
                                    'is_percent'                 => 0,
                                    'pricing_value'              => null,
                                    'use_default_value'          => true
                                );
                            }
                        }
                   }
               }
            }

            foreach ($pricings[0] as $pricing) {
                // Addding pricing to options
                $valueKey = $pricing['oggetto_super_attribute_id'] . ':' . $pricing['value_index'];
                if (isset($values[$valueKey])) {
                    $values[$valueKey]['pricing_value']     = $pricing['pricing_value'];
                    $values[$valueKey]['is_percent']        = $pricing['is_percent'];
                    $values[$valueKey]['value_id']          = $pricing['value_id'];
                    $values[$valueKey]['use_default_value'] = true;
                }
            }

            if ($websiteId && isset($pricings[$websiteId])) {
                foreach ($pricings[$websiteId] as $pricing) {
                    $valueKey = $pricing['oggetto_super_attribute_id'] . ':' . $pricing['value_index'];
                    if (isset($values[$valueKey])) {
                        $values[$valueKey]['pricing_value']     = $pricing['pricing_value'];
                        $values[$valueKey]['is_percent']        = $pricing['is_percent'];
                        $values[$valueKey]['value_id']          = $pricing['value_id'];
                        $values[$valueKey]['use_default_value'] = false;
                    }
                }
            }

            foreach ($values as $data) {
                $this->getItemById($data['oggetto_super_attribute_id'])->addPrice($data);
            }
        }
        return $this;
    }

    /**
     * Retrive oggetto instance
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        return $this->_oggetto;
    }
}
