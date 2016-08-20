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


class Shaurmalab_Score_Model_Convert_Parser_Oggetto
    extends Mage_Eav_Model_Convert_Parser_Abstract
{
    const MULTI_DELIMITER = ' , ';
    protected $_resource;

    /**
     * Oggetto collections per store
     *
     * @var array
     */
    protected $_collections;

    /**
     * Oggetto Type Instances object cache
     *
     * @var array
     */
    protected $_oggettoTypeInstances = array();

    /**
     * Oggetto Type cache
     *
     * @var array
     */
    protected $_oggettoTypes;

    protected $_inventoryFields = array();

    protected $_imageFields = array();

    protected $_systemFields = array();
    protected $_internalFields = array();
    protected $_externalFields = array();

    protected $_inventoryItems = array();

    protected $_oggettoModel;

    protected $_setInstances = array();

    protected $_store;
    protected $_storeId;
    protected $_attributes = array();

    public function __construct()
    {
        foreach (Mage::getConfig()->getFieldset('score_oggetto_dataflow', 'admin') as $code=>$node) {
            if ($node->is('inventory')) {
                $this->_inventoryFields[] = $code;
                if ($node->is('use_config')) {
                    $this->_inventoryFields[] = 'use_config_'.$code;
                }
            }
            if ($node->is('internal')) {
                $this->_internalFields[] = $code;
            }
            if ($node->is('system')) {
                $this->_systemFields[] = $code;
            }
            if ($node->is('external')) {
                $this->_externalFields[$code] = $code;
            }
            if ($node->is('img')) {
                $this->_imageFields[] = $code;
            }
        }
    }

    /**
     * @return Shaurmalab_Score_Model_Mysql4_Convert
     */
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Mage::getResourceSingleton('score_oggetto/convert');
                #->loadStores()
                #->loadOggettos()
                #->loadAttributeSets()
                #->loadAttributeOptions();
        }
        return $this->_resource;
    }

    public function getCollection($storeId)
    {
        if (!isset($this->_collections[$storeId])) {
            $this->_collections[$storeId] = Mage::getResourceModel('score/oggetto_collection');
            $this->_collections[$storeId]->getEntity()->setStore($storeId);
        }
        return $this->_collections[$storeId];
    }

    /**
     * Retrieve oggetto type options
     *
     * @return array
     */
    public function getOggettoTypes()
    {
        if (is_null($this->_oggettoTypes)) {
            $this->_oggettoTypes = Mage::getSingleton('score/oggetto_type')
                ->getOptionArray();
        }
        return $this->_oggettoTypes;
    }

    /**
     * Retrieve Oggetto type name by code
     *
     * @param string $code
     * @return string
     */
    public function getOggettoTypeName($code)
    {
        $oggettoTypes = $this->getOggettoTypes();
        if (isset($oggettoTypes[$code])) {
            return $oggettoTypes[$code];
        }
        return false;
    }

    /**
     * Retrieve oggetto type code by name
     *
     * @param string $name
     * @return string
     */
    public function getOggettoTypeId($name)
    {
        $oggettoTypes = $this->getOggettoTypes();
        if ($code = array_search($name, $oggettoTypes)) {
            return $code;
        }
        return false;
    }

    /**
     * Retrieve oggetto model cache
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggettoModel()
    {
        if (is_null($this->_oggettoModel)) {
            $oggettoModel = Mage::getModel('score/oggetto');
            $this->_oggettoModel = Mage::objects()->save($oggettoModel);
        }
        return Mage::objects()->load($this->_oggettoModel);
    }

    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            try {
                $store = Mage::app()->getStore($this->getVar('store'));
            } catch (Exception $e) {
                $this->addException(
                    Mage::helper('score')->__('Invalid store specified'),
                    Varien_Convert_Exception::FATAL
                );
                throw $e;
            }
            $this->_store = $store;
        }
        return $this->_store;
    }

    /**
     * Retrieve store ID
     *
     * @return int
     */
    public function getStoreId()
    {
        if (is_null($this->_storeId)) {
            $this->_storeId = $this->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * ReDefine Oggetto Type Instance to Oggetto
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Convert_Parser_Oggetto
     */
    public function setOggettoTypeInstance(Shaurmalab_Score_Model_Oggetto $oggetto)
    {
        $type = $oggetto->getTypeId();
        if (!isset($this->_oggettoTypeInstances[$type])) {
            $this->_oggettoTypeInstances[$type] = Mage::getSingleton('score/oggetto_type')
                ->factory($oggetto, true);
        }
        $oggetto->setTypeInstance($this->_oggettoTypeInstances[$type], true);
        return $this;
    }

    public function getAttributeSetInstance()
    {
        $oggettoType = $this->getOggettoModel()->getType();
        $attributeSetId = $this->getOggettoModel()->getAttributeSetId();

        if (!isset($this->_setInstances[$oggettoType][$attributeSetId])) {
            $this->_setInstances[$oggettoType][$attributeSetId] =
                Mage::getSingleton('score/oggetto_type')->factory($this->getOggettoModel());
        }

        return $this->_setInstances[$oggettoType][$attributeSetId];
    }

    /**
     * Retrieve eav entity attribute model
     *
     * @param string $code
     * @return Mage_Eav_Model_Entity_Attribute
     */
    public function getAttribute($code)
    {
        if (!isset($this->_attributes[$code])) {
            $this->_attributes[$code] = $this->getOggettoModel()->getResource()->getAttribute($code);
        }
        return $this->_attributes[$code];
    }

    /**
     * @deprecated not used anymore
     */
    public function parse()
    {
        $data            = $this->getData();
        $entityTypeId    = Mage::getSingleton('eav/config')->getEntityType(Shaurmalab_Score_Model_Oggetto::ENTITY)->getId();
        $inventoryFields = array();

        foreach ($data as $i=>$row) {
            $this->setPosition('Line: '.($i+1));
            try {
                // validate SKU
                if (empty($row['sku'])) {
                    $this->addException(
                        Mage::helper('score')->__('Missing SKU, skipping the record.'),
                        Mage_Dataflow_Model_Convert_Exception::ERROR
                    );
                    continue;
                }
                $this->setPosition('Line: '.($i+1).', SKU: '.$row['sku']);

                // try to get entity_id by sku if not set
                if (empty($row['entity_id'])) {
                    $row['entity_id'] = $this->getResource()->getOggettoIdBySku($row['sku']);
                }

                // if attribute_set not set use default
                if (empty($row['attribute_set'])) {
                    $row['attribute_set'] = 'Default';
                }
                // get attribute_set_id, if not throw error
                $row['attribute_set_id'] = $this->getAttributeSetId($entityTypeId, $row['attribute_set']);
                if (!$row['attribute_set_id']) {
                    $this->addException(
                        Mage::helper('score')->__('Invalid attribute set specified, skipping the record.'),
                        Mage_Dataflow_Model_Convert_Exception::ERROR
                    );
                    continue;
                }

                if (empty($row['type'])) {
                    $row['type'] = 'Simple';
                }
                // get oggetto type_id, if not throw error
                $row['type_id'] = $this->getOggettoTypeId($row['type']);
                if (!$row['type_id']) {
                    $this->addException(
                        Mage::helper('score')->__('Invalid oggetto type specified, skipping the record.'),
                        Mage_Dataflow_Model_Convert_Exception::ERROR
                    );
                    continue;
                }

                // get store ids
                $storeIds = $this->getStoreIds(isset($row['store']) ? $row['store'] : $this->getVar('store'));
                if (!$storeIds) {
                    $this->addException(
                        Mage::helper('score')->__('Invalid store specified, skipping the record.'),
                        Mage_Dataflow_Model_Convert_Exception::ERROR
                    );
                    continue;
                }

                // import data
                $rowError = false;
                foreach ($storeIds as $storeId) {
                    $collection = $this->getCollection($storeId);
                    $entity = $collection->getEntity();

                    $model = Mage::getModel('score/oggetto');
                    $model->setStoreId($storeId);
                    if (!empty($row['entity_id'])) {
                        $model->load($row['entity_id']);
                    }
                    foreach ($row as $field=>$value) {
                        $attribute = $entity->getAttribute($field);

                        if (!$attribute) {
                            //$inventoryFields[$row['sku']][$field] = $value;

                            if (in_array($field, $this->_inventoryFields)) {
                                $inventoryFields[$row['sku']][$field] = $value;
                            }
                            continue;
//                            $this->addException(
//                                Mage::helper('score')->__('Unknown attribute: %s.', $field),
//                                Mage_Dataflow_Model_Convert_Exception::ERROR
//                            );
                        }
                        if ($attribute->usesSource()) {
                            $source = $attribute->getSource();
                            $optionId = $this->getSourceOptionId($source, $value);
                            if (is_null($optionId)) {
                                $rowError = true;
                                $this->addException(
                                    Mage::helper('score')->__('Invalid attribute option specified for attribute %s (%s), skipping the record.', $field, $value),
                                    Mage_Dataflow_Model_Convert_Exception::ERROR
                                );
                                continue;
                            }
                            $value = $optionId;
                        }
                        $model->setData($field, $value);

                    }//foreach ($row as $field=>$value)

                    //echo 'Before **********************<br/><pre>';
                    //print_r($model->getData());
                    if (!$rowError) {
                        $collection->addItem($model);
                    }
                    unset($model);
                } //foreach ($storeIds as $storeId)
            } catch (Exception $e) {
                if (!$e instanceof Mage_Dataflow_Model_Convert_Exception) {
                    $this->addException(
                        Mage::helper('score')->__('Error during retrieval of option value: %s', $e->getMessage()),
                        Mage_Dataflow_Model_Convert_Exception::FATAL
                    );
                }
            }
        }

        // set importinted to adaptor
        if (sizeof($inventoryFields) > 0) {
            Mage::register('current_imported_inventory', $inventoryFields);
            //$this->setInventoryItems($inventoryFields);
        } // end setting imported to adaptor

        $this->setData($this->_collections);
        return $this;
    }

    public function setInventoryItems($items)
    {
        $this->_inventoryItems = $items;
    }

    public function getInventoryItems()
    {
        return $this->_inventoryItems;
    }

    /**
     * Unparse (prepare data) loaded oggettos
     *
     * @return Shaurmalab_Score_Model_Convert_Parser_Oggetto
     */
    public function unparse()
    {
        $entityIds = $this->getData();

        foreach ($entityIds as $i => $entityId) {
            $oggetto = $this->getOggettoModel()
                ->setStoreId($this->getStoreId())
                ->load($entityId);
            $this->setOggettoTypeInstance($oggetto);
            /* @var $oggetto Shaurmalab_Score_Model_Oggetto */

            $position = Mage::helper('score')->__('Line %d, SKU: %s', ($i+1), $oggetto->getSku());
            $this->setPosition($position);

            $row = array(
                'store'         => $this->getStore()->getCode(),
                'websites'      => '',
                'attribute_set' => $this->getAttributeSetName($oggetto->getEntityTypeId(),
                                        $oggetto->getAttributeSetId()),
                'type'          => $oggetto->getTypeId(),
                'category_ids'  => join(',', $oggetto->getCategoryIds())
            );

            if ($this->getStore()->getCode() == Mage_Core_Model_Store::ADMIN_CODE) {
                $websiteCodes = array();
                foreach ($oggetto->getWebsiteIds() as $websiteId) {
                    $websiteCode = Mage::app()->getWebsite($websiteId)->getCode();
                    $websiteCodes[$websiteCode] = $websiteCode;
                }
                $row['websites'] = join(',', $websiteCodes);
            } else {
                $row['websites'] = $this->getStore()->getWebsite()->getCode();
                if ($this->getVar('url_field')) {
                    $row['url'] = $oggetto->getOggettoUrl(false);
                }
            }

            foreach ($oggetto->getData() as $field => $value) {
                if (in_array($field, $this->_systemFields) || is_object($value)) {
                    continue;
                }

                $attribute = $this->getAttribute($field);
                if (!$attribute) {
                    continue;
                }

                if ($attribute->usesSource()) {
                    $option = $attribute->getSource()->getOptionText($value);
                    if ($value && empty($option) && $option != '0') {
                        $this->addException(
                            Mage::helper('score')->__('Invalid option ID specified for %s (%s), skipping the record.', $field, $value),
                            Mage_Dataflow_Model_Convert_Exception::ERROR
                        );
                        continue;
                    }
                    if (is_array($option)) {
                        $value = join(self::MULTI_DELIMITER, $option);
                    } else {
                        $value = $option;
                    }
                    unset($option);
                } elseif (is_array($value)) {
                    continue;
                }

                $row[$field] = $value;
            }

            if ($stockItem = $oggetto->getStockItem()) {
                foreach ($stockItem->getData() as $field => $value) {
                    if (in_array($field, $this->_systemFields) || is_object($value)) {
                        continue;
                    }
                    $row[$field] = $value;
                }
            }

            foreach ($this->_imageFields as $field) {
                if (isset($row[$field]) && $row[$field] == 'no_selection') {
                    $row[$field] = null;
                }
            }

            $batchExport = $this->getBatchExportModel()
                ->setId(null)
                ->setBatchId($this->getBatchModel()->getId())
                ->setBatchData($row)
                ->setStatus(1)
                ->save();
            $oggetto->reset();
        }

        return $this;
    }

    /**
     * Retrieve accessible external oggetto attributes
     *
     * @return array
     */
    public function getExternalAttributes()
    {
        $oggettoAttributes  = Mage::getResourceModel('score/oggetto_attribute_collection')->load();
        $attributes         = $this->_externalFields;

        foreach ($oggettoAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $this->_internalFields) || $attr->getFrontendInput() == 'hidden') {
                continue;
            }
            $attributes[$code] = $code;
        }

        foreach ($this->_inventoryFields as $field) {
            $attributes[$field] = $field;
        }

        return $attributes;
    }
}
