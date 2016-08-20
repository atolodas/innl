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


class Shaurmalab_Score_Model_Convert_Adapter_Oggetto
    extends Mage_Eav_Model_Convert_Adapter_Entity
{
    const MULTI_DELIMITER   = ' , ';
    const ENTITY            = 'score_oggetto_import';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'score_oggetto_import';

    /**
     * Oggetto model
     *
     * @var Shaurmalab_Score_Model_Oggetto
     */
    protected $_oggettoModel;

    /**
     * oggetto types collection array
     *
     * @var array
     */
    protected $_oggettoTypes;

    /**
     * Oggetto Type Instances singletons
     *
     * @var array
     */
    protected $_oggettoTypeInstances = array();

    /**
     * oggetto attribute set collection array
     *
     * @var array
     */
    protected $_oggettoAttributeSets;

    protected $_stores;

    protected $_attributes = array();

    protected $_configs = array();

    protected $_requiredFields = array();

    protected $_ignoreFields = array();

    /**
     * @deprecated after 1.5.0.0-alpha2
     *
     * @var array
     */
    protected $_imageFields = array();

    /**
     * Inventory Fields array
     *
     * @var array
     */
    protected $_inventoryFields             = array();

    /**
     * Inventory Fields by oggetto Types
     *
     * @var array
     */
    protected $_inventoryFieldsOggettoTypes = array();

    protected $_toNumber = array();

    /**
     * Retrieve event prefix for adapter
     *
     * @return string
     */
    public function getEventPrefix()
    {
        return $this->_eventPrefix;
    }

    /**
     * Affected entity ids
     *
     * @var array
     */
    protected $_affectedEntityIds = array();

    /**
     * Store affected entity ids
     *
     * @param  int|array $ids
     * @return Shaurmalab_Score_Model_Convert_Adapter_Oggetto
     */
    protected function _addAffectedEntityIds($ids)
    {
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $this->_addAffectedEntityIds($id);
            }
        } else {
            $this->_affectedEntityIds[] = $ids;
        }

        return $this;
    }

    /**
     * Retrieve affected entity ids
     *
     * @return array
     */
    public function getAffectedEntityIds()
    {
        return $this->_affectedEntityIds;
    }

    /**
     * Clear affected entity ids results
     *
     * @return Shaurmalab_Score_Model_Convert_Adapter_Oggetto
     */
    public function clearAffectedEntityIds()
    {
        $this->_affectedEntityIds = array();
        return $this;
    }

    /**
     * Load oggetto collection Id(s)
     */
    public function load()
    {
        $attrFilterArray = array();
        $attrFilterArray ['name']           = 'like';
        $attrFilterArray ['sku']            = 'startsWith';
        $attrFilterArray ['type']           = 'eq';
        $attrFilterArray ['attribute_set']  = 'eq';
        $attrFilterArray ['visibility']     = 'eq';
        $attrFilterArray ['status']         = 'eq';
        $attrFilterArray ['price']          = 'fromTo';
        $attrFilterArray ['qty']            = 'fromTo';
        $attrFilterArray ['store_id']       = 'eq';

        $attrToDb = array(
            'type'          => 'type_id',
            'attribute_set' => 'attribute_set_id'
        );

        $filters = $this->_parseVars();

        if ($qty = $this->getFieldValue($filters, 'qty')) {
            $qtyFrom = isset($qty['from']) ? (float) $qty['from'] : 0;
            $qtyTo   = isset($qty['to']) ? (float) $qty['to'] : 0;

            $qtyAttr = array();
            $qtyAttr['alias']       = 'qty';
            $qtyAttr['attribute']   = 'cataloginventory/stock_item';
            $qtyAttr['field']       = 'qty';
            $qtyAttr['bind']        = 'oggetto_id=entity_id';
            $qtyAttr['cond']        = "{{table}}.qty between '{$qtyFrom}' AND '{$qtyTo}'";
            $qtyAttr['joinType']    = 'inner';

            $this->setJoinField($qtyAttr);
        }

        parent::setFilter($attrFilterArray, $attrToDb);

        if ($price = $this->getFieldValue($filters, 'price')) {
            $this->_filter[] = array(
                'attribute' => 'price',
                'from'      => $price['from'],
                'to'        => $price['to']
            );
            $this->setJoinAttr(array(
                'alias'     => 'price',
                'attribute' => 'score_oggetto/price',
                'bind'      => 'entity_id',
                'joinType'  => 'LEFT'
            ));
        }

        return parent::load();
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
        if ($this->_attributes[$code] instanceof Shaurmalab_Score_Model_Resource_Eav_Attribute) {
            $applyTo = $this->_attributes[$code]->getApplyTo();
            if ($applyTo && !in_array($this->getOggettoModel()->getTypeId(), $applyTo)) {
                return false;
            }
        }
        return $this->_attributes[$code];
    }

    /**
     * Retrieve oggetto type collection array
     *
     * @return array
     */
    public function getOggettoTypes()
    {
        if (is_null($this->_oggettoTypes)) {
            $this->_oggettoTypes = array();
            $options = Mage::getModel('score/oggetto_type')
                ->getOptionArray();
            foreach ($options as $k => $v) {
                $this->_oggettoTypes[$k] = $k;
            }
        }
        return $this->_oggettoTypes;
    }

    /**
     * ReDefine Oggetto Type Instance to Oggetto
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Convert_Adapter_Oggetto
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

    /**
     * Retrieve oggetto attribute set collection array
     *
     * @return array
     */
    public function getOggettoAttributeSets()
    {
        if (is_null($this->_oggettoAttributeSets)) {
            $this->_oggettoAttributeSets = array();

            $entityTypeId = Mage::getModel('eav/entity')
                ->setType('score_oggetto')
                ->getTypeId();
            $collection = Mage::getResourceModel('eav/entity_attribute_set_collection')
                ->setEntityTypeFilter($entityTypeId);
            foreach ($collection as $set) {
                $this->_oggettoAttributeSets[$set->getAttributeSetName()] = $set->getId();
            }
        }
        return $this->_oggettoAttributeSets;
    }

    /**
     *  Init stores
     */
    protected function _initStores ()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true, true);
            foreach ($this->_stores as $code => $store) {
                $this->_storesIdCode[$store->getId()] = $code;
            }
        }
    }

    /**
     * Retrieve store object by code
     *
     * @param string $store
     * @return Mage_Core_Model_Store
     */
    public function getStoreByCode($store)
    {
        $this->_initStores();
        /**
         * In single store mode all data should be saved as default
         */
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID);
        }

        if (isset($this->_stores[$store])) {
            return $this->_stores[$store];
        }

        return false;
    }

    /**
     * Retrieve store object by code
     *
     * @param string $store
     * @return Mage_Core_Model_Store
     */
    public function getStoreById($id)
    {
        $this->_initStores();
        /**
         * In single store mode all data should be saved as default
         */
        if (Mage::app()->isSingleStoreMode()) {
            return Mage::app()->getStore(Shaurmalab_Score_Model_Abstract::DEFAULT_STORE_ID);
        }

        if (isset($this->_storesIdCode[$id])) {
            return $this->getStoreByCode($this->_storesIdCode[$id]);
        }

        return false;
    }

    public function parse()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            //print '<pre>'.memory_get_usage().'</pre>';
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    protected $_oggettoId = '';

    /**
     * Initialize convert adapter model for oggettos collection
     *
     */
    public function __construct()
    {
        $fieldset = Mage::getConfig()->getFieldset('score_oggetto_dataflow', 'admin');
        foreach ($fieldset as $code => $node) {
            /* @var $node Mage_Core_Model_Config_Element */
            if ($node->is('inventory')) {
                foreach ($node->oggetto_type->children() as $oggettoType) {
                    $oggettoType = $oggettoType->getName();
                    $this->_inventoryFieldsOggettoTypes[$oggettoType][] = $code;
                    if ($node->is('use_config')) {
                        $this->_inventoryFieldsOggettoTypes[$oggettoType][] = 'use_config_' . $code;
                    }
                }

                $this->_inventoryFields[] = $code;
                if ($node->is('use_config')) {
                    $this->_inventoryFields[] = 'use_config_'.$code;
                }
            }
            if ($node->is('required')) {
                $this->_requiredFields[] = $code;
            }
            if ($node->is('ignore')) {
                $this->_ignoreFields[] = $code;
            }
            if ($node->is('to_number')) {
                $this->_toNumber[] = $code;
            }
        }

        $this->setVar('entity_type', 'score/oggetto');
        if (!Mage::registry('Object_Cache_Oggetto')) {
            $this->setOggetto(Mage::getModel('score/oggetto'));
        }

        if (!Mage::registry('Object_Cache_StockItem')) {
            $this->setStockItem(Mage::getModel('cataloginventory/stock_item'));
        }
    }

    /**
     * Retrieve not loaded collection
     *
     * @param string $entityType
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Collection
     */
    protected function _getCollectionForLoad($entityType)
    {
        $collection = parent::_getCollectionForLoad($entityType)
            ->setStoreId($this->getStoreId())
            ->addStoreFilter($this->getStoreId());
        return $collection;
    }

    public function setOggetto(Shaurmalab_Score_Model_Oggetto $object)
    {
        $id = Mage::objects()->save($object);
        //$this->_oggetto = $object;
        Mage::register('Object_Cache_Oggetto', $id);
    }

    public function getOggetto()
    {
        return Mage::objects()->load(Mage::registry('Object_Cache_Oggetto'));
    }

    public function setStockItem(Mage_CatalogInventory_Model_Stock_Item $object)
    {
        $id = Mage::objects()->save($object);
        Mage::register('Object_Cache_StockItem', $id);
    }

    public function getStockItem()
    {
        return Mage::objects()->load(Mage::registry('Object_Cache_StockItem'));
    }

    public function save()
    {
        $stores = array();
        foreach (Mage::getConfig()->getNode('stores')->children() as $storeNode) {
            $stores[(int)$storeNode->system->store->id] = $storeNode->getName();
        }

        $collections = $this->getData();
        if ($collections instanceof Shaurmalab_Score_Model_Oggetto_Oggetto_Collection) {
            $collections = array($collections->getEntity()->getStoreId()=>$collections);
        } elseif (!is_array($collections)) {
            $this->addException(
                Mage::helper('score')->__('No oggetto collections found.'),
                Mage_Dataflow_Model_Convert_Exception::FATAL
            );
        }

        $stockItems = Mage::registry('current_imported_inventory');
        if ($collections) foreach ($collections as $storeId=>$collection) {
            $this->addException(Mage::helper('score')->__('Records for "%s" store found.', $stores[$storeId]));

            if (!$collection instanceof Shaurmalab_Score_Model_Oggetto_Oggetto_Collection) {
                $this->addException(
                    Mage::helper('score')->__('Oggetto collection expected.'),
                    Mage_Dataflow_Model_Convert_Exception::FATAL
                );
            }
            try {
                $i = 0;
                foreach ($collection->getIterator() as $model) {
                    $new = false;
                    // if oggetto is new, create default values first
                    if (!$model->getId()) {
                        $new = true;
                        $model->save();

                        // if new oggetto and then store is not default
                        // we duplicate oggetto as default oggetto with store_id -
                        if (0 !== $storeId ) {
                            $data = $model->getData();
                            $default = Mage::getModel('score/oggetto');
                            $default->setData($data);
                            $default->setStoreId(0);
                            $default->save();
                            unset($default);
                        } // end

                        #Mage::getResourceSingleton('score_oggetto/convert')->addOggettoToStore($model->getId(), 0);
                    }
                    if (!$new || 0!==$storeId) {
                        if (0!==$storeId) {
                            Mage::getResourceSingleton('score_oggetto/convert')->addOggettoToStore(
                                $model->getId(),
                                $storeId
                            );
                        }
                        $model->save();
                    }

                    if (isset($stockItems[$model->getSku()]) && $stock = $stockItems[$model->getSku()]) {
                        $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByOggetto($model->getId());
                        $stockItemId = $stockItem->getId();

                        if (!$stockItemId) {
                            $stockItem->setData('oggetto_id', $model->getId());
                            $stockItem->setData('stock_id', 1);
                            $data = array();
                        } else {
                            $data = $stockItem->getData();
                        }

                        foreach($stock as $field => $value) {
                            if (!$stockItemId) {
                                if (in_array($field, $this->_configs)) {
                                    $stockItem->setData('use_config_'.$field, 0);
                                }
                                $stockItem->setData($field, $value?$value:0);
                            } else {

                                if (in_array($field, $this->_configs)) {
                                    if ($data['use_config_'.$field] == 0) {
                                        $stockItem->setData($field, $value?$value:0);
                                    }
                                } else {
                                    $stockItem->setData($field, $value?$value:0);
                                }
                            }
                        }
                        $stockItem->save();
                        unset($data);
                        unset($stockItem);
                        unset($stockItemId);
                    }
                    unset($model);
                    $i++;
                }
                $this->addException(Mage::helper('score')->__("Saved %d record(s)", $i));
            } catch (Exception $e) {
                if (!$e instanceof Mage_Dataflow_Model_Convert_Exception) {
                    $this->addException(
                        Mage::helper('score')->__('An error occurred while saving the collection, aborting. Error message: %s', $e->getMessage()),
                        Mage_Dataflow_Model_Convert_Exception::FATAL
                    );
                }
            }
        }
        unset($collections);

        return $this;
    }

    /**
     * Save oggetto (import)
     *
     * @param  array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
        $oggetto = $this->getOggettoModel()
            ->reset();

        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('score')->__('Skipping import row, required field "%s" is not defined.', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('score')->__('Skipping import row, store "%s" field does not exist.', $importData['store']);
            Mage::throwException($message);
        }

        if (empty($importData['sku'])) {
            $message = Mage::helper('score')->__('Skipping import row, required field "%s" is not defined.', 'sku');
            Mage::throwException($message);
        }
        $oggetto->setStoreId($store->getId());
        $oggettoId = $oggetto->getIdBySku($importData['sku']);

        if ($oggettoId) {
            $oggetto->load($oggettoId);
        } else {
            $oggettoTypes = $this->getOggettoTypes();
            $oggettoAttributeSets = $this->getOggettoAttributeSets();

            /**
             * Check oggetto define type
             */
            if (empty($importData['type']) || !isset($oggettoTypes[strtolower($importData['type'])])) {
                $value = isset($importData['type']) ? $importData['type'] : '';
                $message = Mage::helper('score')->__('Skip import row, is not valid value "%s" for field "%s"', $value, 'type');
                Mage::throwException($message);
            }
            $oggetto->setTypeId($oggettoTypes[strtolower($importData['type'])]);
            /**
             * Check oggetto define attribute set
             */
            if (empty($importData['attribute_set']) || !isset($oggettoAttributeSets[$importData['attribute_set']])) {
                $value = isset($importData['attribute_set']) ? $importData['attribute_set'] : '';
                $message = Mage::helper('score')->__('Skip import row, the value "%s" is invalid for field "%s"', $value, 'attribute_set');
                Mage::throwException($message);
            }
            $oggetto->setAttributeSetId($oggettoAttributeSets[$importData['attribute_set']]);

            foreach ($this->_requiredFields as $field) {
                $attribute = $this->getAttribute($field);
                if (!isset($importData[$field]) && $attribute && $attribute->getIsRequired()) {
                    $message = Mage::helper('score')->__('Skipping import row, required field "%s" for new oggettos is not defined.', $field);
                    Mage::throwException($message);
                }
            }
        }

        $this->setOggettoTypeInstance($oggetto);

        if (isset($importData['category_ids'])) {
            $oggetto->setCategoryIds($importData['category_ids']);
        }

        foreach ($this->_ignoreFields as $field) {
            if (isset($importData[$field])) {
                unset($importData[$field]);
            }
        }

        if ($store->getId() != 0) {
            $websiteIds = $oggetto->getWebsiteIds();
            if (!is_array($websiteIds)) {
                $websiteIds = array();
            }
            if (!in_array($store->getWebsiteId(), $websiteIds)) {
                $websiteIds[] = $store->getWebsiteId();
            }
            $oggetto->setWebsiteIds($websiteIds);
        }

        if (isset($importData['websites'])) {
            $websiteIds = $oggetto->getWebsiteIds();
            if (!is_array($websiteIds) || !$store->getId()) {
                $websiteIds = array();
            }
            $websiteCodes = explode(',', $importData['websites']);
            foreach ($websiteCodes as $websiteCode) {
                try {
                    $website = Mage::app()->getWebsite(trim($websiteCode));
                    if (!in_array($website->getId(), $websiteIds)) {
                        $websiteIds[] = $website->getId();
                    }
                } catch (Exception $e) {}
            }
            $oggetto->setWebsiteIds($websiteIds);
            unset($websiteIds);
        }

        foreach ($importData as $field => $value) {
            if (in_array($field, $this->_inventoryFields)) {
                continue;
            }
            if (is_null($value)) {
                continue;
            }

            $attribute = $this->getAttribute($field);
            if (!$attribute) {
                continue;
            }

            $isArray = false;
            $setValue = $value;

            if ($attribute->getFrontendInput() == 'multiselect') {
                $value = explode(self::MULTI_DELIMITER, $value);
                $isArray = true;
                $setValue = array();
            }

            if ($value && $attribute->getBackendType() == 'decimal') {
                $setValue = $this->getNumber($value);
            }

            if ($attribute->usesSource()) {
                $options = $attribute->getSource()->getAllOptions(false);

                if ($isArray) {
                    foreach ($options as $item) {
                        if (in_array($item['label'], $value)) {
                            $setValue[] = $item['value'];
                        }
                    }
                } else {
                    $setValue = false;
                    foreach ($options as $item) {
                        if (is_array($item['value'])) {
                            foreach ($item['value'] as $subValue) {
                                if (isset($subValue['value']) && $subValue['value'] == $value) {
                                    $setValue = $value;
                                }
                            }
                        } else if ($item['label'] == $value) {
                            $setValue = $item['value'];
                        }
                    }
                }
            }

            $oggetto->setData($field, $setValue);
        }

        if (!$oggetto->getVisibility()) {
            $oggetto->setVisibility(Shaurmalab_Score_Model_Oggetto_Visibility::VISIBILITY_NOT_VISIBLE);
        }

        $stockData = array();
        $inventoryFields = isset($this->_inventoryFieldsOggettoTypes[$oggetto->getTypeId()])
            ? $this->_inventoryFieldsOggettoTypes[$oggetto->getTypeId()]
            : array();
        foreach ($inventoryFields as $field) {
            if (isset($importData[$field])) {
                if (in_array($field, $this->_toNumber)) {
                    $stockData[$field] = $this->getNumber($importData[$field]);
                } else {
                    $stockData[$field] = $importData[$field];
                }
            }
        }
        $oggetto->setStockData($stockData);

        $mediaGalleryBackendModel = $this->getAttribute('media_gallery')->getBackend();

        $arrayToMassAdd = array();

        foreach ($oggetto->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            if (isset($importData[$mediaAttributeCode])) {
                $file = trim($importData[$mediaAttributeCode]);
                if (!empty($file) && !$mediaGalleryBackendModel->getImage($oggetto, $file)) {
                    $arrayToMassAdd[] = array('file' => trim($file), 'mediaAttribute' => $mediaAttributeCode);
                }
            }
        }

        $addedFilesCorrespondence = $mediaGalleryBackendModel->addImagesWithDifferentMediaAttributes(
            $oggetto,
            $arrayToMassAdd, Mage::getBaseDir('media') . DS . 'import',
            false,
            false
        );

        foreach ($oggetto->getMediaAttributes() as $mediaAttributeCode => $mediaAttribute) {
            $addedFile = '';
            if (isset($importData[$mediaAttributeCode . '_label'])) {
                $fileLabel = trim($importData[$mediaAttributeCode . '_label']);
                if (isset($importData[$mediaAttributeCode])) {
                    $keyInAddedFile = array_search($importData[$mediaAttributeCode],
                        $addedFilesCorrespondence['alreadyAddedFiles']);
                    if ($keyInAddedFile !== false) {
                        $addedFile = $addedFilesCorrespondence['alreadyAddedFilesNames'][$keyInAddedFile];
                    }
                }

                if (!$addedFile) {
                    $addedFile = $oggetto->getData($mediaAttributeCode);
                }
                if ($fileLabel && $addedFile) {
                    $mediaGalleryBackendModel->updateImage($oggetto, $addedFile, array('label' => $fileLabel));
                }
            }
        }

        $oggetto->setIsMassupdate(true);
        $oggetto->setExcludeUrlRewrite(true);

        $oggetto->save();

        // Store affected oggettos ids
        $this->_addAffectedEntityIds($oggetto->getId());

        return true;
    }

    /**
     * Silently save oggetto (import)
     *
     * @param  array $importData
     * @return bool
     */
    public function saveRowSilently(array $importData)
    {
        try {
            $result = $this->saveRow($importData);
            return $result;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Process after import data
     * Init indexing process after score oggetto import
     */
    public function finish()
    {
        /**
         * Back compatibility event
         */
        Mage::dispatchEvent($this->_eventPrefix . '_after', array());

        $entity = new Varien_Object();
        Mage::getSingleton('index/indexer')->processEntityAction(
            $entity, self::ENTITY, Mage_Index_Model_Event::TYPE_SAVE
        );
    }
}
