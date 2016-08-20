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
 * Score category helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Helper_Oggetto extends Mage_Core_Helper_Url
{
    const XML_PATH_OGGETTO_URL_SUFFIX = 'score/seo/oggetto_url_suffix';
    const XML_PATH_OGGETTO_URL_USE_CATEGORY = 'score/seo/oggetto_use_categories';
    const XML_PATH_USE_OGGETTO_CANONICAL_TAG = 'score/seo/oggetto_canonical_tag';

    /**
     * Flag that shows if Magento has to check oggetto to be saleable (enabled and/or inStock)
     *
     * @var boolean
     */
    protected $_skipSaleableCheck = false;

    /**
     * Cache for oggetto rewrite suffix
     *
     * @var array
     */
    protected $_oggettoUrlSuffix = array();

    protected $_statuses;

    protected $_priceBlock;

    /**
     * Retrieve oggetto view page url
     *
     * @param   mixed $oggetto
     * @return  string
     */
    public function getOggettoUrl($oggetto)
    {
        if ($oggetto instanceof Shaurmalab_Score_Model_Oggetto) {
            return $oggetto->getOggettoUrl();
        } elseif (is_numeric($oggetto)) {
            return Mage::getModel('score/oggetto')->load($oggetto)->getOggettoUrl();
        }
        return false;
    }

    /**
     * Retrieve oggetto price
     *
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  float
     */
    public function getPrice($oggetto)
    {
        return $oggetto->getPrice();
    }

    /**
     * Retrieve oggetto final price
     *
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  float
     */
    public function getFinalPrice($oggetto)
    {
        return $oggetto->getFinalPrice();
    }

    /**
     * Retrieve base image url
     *
     * @return string
     */
    public function getImageUrl($oggetto)
    {
        $url = false;
        if (!$oggetto->getImage()) {
            $url = Mage::getDesign()->getSkinUrl('images/no_image.jpg');
        } elseif ($attribute = $oggetto->getResource()->getAttribute('image')) {
            $url = $attribute->getFrontend()->getUrl($oggetto);
        }
        return $url;
    }

    /**
     * Retrieve small image url
     *
     * @return unknown
     */
    public function getSmallImageUrl($oggetto)
    {
        $url = false;
        if (!$oggetto->getImage()) {
            $url = Mage::getDesign()->getSkinUrl('images/no_image.jpg');
        } elseif ($attribute = $oggetto->getResource()->getAttribute('image')) {
            $url = $attribute->getFrontend()->getUrl($oggetto);
        }
        return $url;
    }

    /**
     * Retrieve thumbnail image url
     *
     * @return unknown
     */
    public function getThumbnailUrl($oggetto)
    {
        return '';
    }

    public function getEmailToFriendUrl($oggetto)
    {
        $categoryId = null;
        if ($category = Mage::registry('current_category')) {
            $categoryId = $category->getId();
        }
        return $this->_getUrl('sendfriend/oggetto/send', array(
            'id' => $oggetto->getId(),
            'cat_id' => $categoryId
        ));
    }

    public function getStatuses()
    {
        if (is_null($this->_statuses)) {
            $this->_statuses = array(); //Mage::getModel('score/oggetto_status')->getResourceCollection()->load();
        }

        return $this->_statuses;
    }

    /**
     * Check if a oggetto can be shown
     *
     * @param  Shaurmalab_Score_Model_Oggetto|int $oggetto
     * @return boolean
     */
    public function canShow($oggetto, $where = 'score')
    {
        if (is_int($oggetto)) {
            $oggetto = Mage::getModel('score/oggetto')->load($oggetto);
        }

        /* @var $oggetto Shaurmalab_Score_Model_Oggetto */

        if (!$oggetto->getId()) {
            return false;
        }
        // TODO: use it later for hidding unpublic oggettos
        return true; //$oggetto->isVisibleInScore() && $oggetto->isVisibleInSiteVisibility();
    }

    /**
     * Retrieve oggetto rewrite sufix for store
     *
     * @param int $storeId
     * @return string
     */
    public function getOggettoUrlSuffix($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = Mage::app()->getStore()->getId();
        }

        if (!isset($this->_oggettoUrlSuffix[$storeId])) {
            $this->_oggettoUrlSuffix[$storeId] = Mage::getStoreConfig(self::XML_PATH_OGGETTO_URL_SUFFIX, $storeId);
        }
        return $this->_oggettoUrlSuffix[$storeId];
    }

    /**
     * Check if <link rel="canonical"> can be used for oggetto
     *
     * @param $store
     * @return bool
     */
    public function canUseCanonicalTag($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_USE_OGGETTO_CANONICAL_TAG, $store);
    }

    /**
     * Return information array of oggetto attribute input types
     * Only a small number of settings returned, so we won't break anything in current dataflow
     * As soon as development process goes on we need to add there all possible settings
     *
     * @param string $inputType
     * @return array
     */
    public function getAttributeInputTypes($inputType = null)
    {
        /**
         * @todo specify there all relations for properties depending on input type
         */
        $inputTypes = array(
            'multiselect' => array(
                'backend_model' => 'eav/entity_attribute_backend_array'
            ),
            'boolean' => array(
                'source_model' => 'eav/entity_attribute_source_boolean'
            )
        );

        if (is_null($inputType)) {
            return $inputTypes;
        } else if (isset($inputTypes[$inputType])) {
            return $inputTypes[$inputType];
        }
        return array();
    }

    /**
     * Return default attribute backend model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeBackendModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['backend_model'])) {
            return $inputTypes[$inputType]['backend_model'];
        }
        return null;
    }

    /**
     * Return default attribute source model by input type
     *
     * @param string $inputType
     * @return string|null
     */
    public function getAttributeSourceModelByInputType($inputType)
    {
        $inputTypes = $this->getAttributeInputTypes();
        if (!empty($inputTypes[$inputType]['source_model'])) {
            return $inputTypes[$inputType]['source_model'];
        }
        return null;
    }

    /**
     * Inits oggetto to be used for oggetto controller actions and layouts
     * $params can have following data:
     *   'category_id' - id of category to check and append to oggetto as current.
     *     If empty (except FALSE) - will be guessed (e.g. from last visited) to load as current.
     *
     * @param int $oggettoId
     * @param Mage_Core_Controller_Front_Action $controller
     * @param Varien_Object $params
     *
     * @return false|Shaurmalab_Score_Model_Oggetto
     */
    public function initOggetto($oggettoId, $controller, $params = null)
    {
        // Prepare data for routine
        if (!$params) {
            $params = new Varien_Object();
        }

        // Init and load oggetto
        Mage::dispatchEvent('score_controller_oggetto_init_before', array(
            'controller_action' => $controller,
            'params' => $params,
        ));
        if (!$oggettoId) {
            return false;
        }

        $oggetto = Mage::getModel('score/oggetto')
            //->setStoreId(Mage::app()->getStore()->getId())
            ->load($oggettoId);

        if (!$this->canShow($oggetto)) {
            return false;
        }
        if (!in_array(Mage::app()->getStore()->getWebsiteId(), $oggetto->getWebsiteIds())) {
            //  return false;
        }

        // Load oggetto current category
        /* $categoryId = $params->getCategoryId();
        if (!$categoryId && ($categoryId !== false)) {
            $lastId = Mage::getSingleton('score/session')->getLastVisitedCategoryId();
            if ($oggetto->canBeShowInCategory($lastId)) {
                $categoryId = $lastId;
            }
        } elseif (!$oggetto->canBeShowInCategory($categoryId)) {
            $categoryId = null;
        } */

        /* if ($categoryId) {
            $category = Mage::getModel('score/category')->load($categoryId);
            $oggetto->setCategory($category);
            Mage::register('current_category', $category);
        } */

        // Register current data and dispatch final events
        Mage::register('current_oggetto', $oggetto);
        Mage::register('oggetto', $oggetto);

        try {
            Mage::dispatchEvent('score_controller_oggetto_init', array('oggetto' => $oggetto));
            Mage::dispatchEvent('score_controller_oggetto_init_after',
                array('oggetto' => $oggetto,
                    'controller_action' => $controller
                )
            );
        } catch (Mage_Core_Exception $e) {
            Mage::logException($e);
            return false;
        }

        return $oggetto;
    }

    /**
     * Prepares oggetto options by buyRequest: retrieves values and assigns them as default.
     * Also parses and adds oggetto management related values - e.g. qty
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @param  Varien_Object $buyRequest
     * @return Shaurmalab_Score_Helper_Oggetto
     */
    public function prepareOggettoOptions($oggetto, $buyRequest)
    {
        $optionValues = $oggetto->processBuyRequest($buyRequest);
        $optionValues->setQty($buyRequest->getQty());
        $oggetto->setPreconfiguredValues($optionValues);

        return $this;
    }

    /**
     * Process $buyRequest and sets its options before saving configuration to some oggetto item.
     * This method is used to attach additional parameters to processed buyRequest.
     *
     * $params holds parameters of what operation must be performed:
     * - 'current_config', Varien_Object or array - current buyRequest that configures oggetto in this item,
     *   used to restore currently attached files
     * - 'files_prefix': string[a-z0-9_] - prefix that was added at frontend to names of file inputs,
     *   so they won't intersect with other submitted options
     *
     * @param Varien_Object|array $buyRequest
     * @param Varien_Object|array $params
     * @return Varien_Object
     */
    public function addParamsToBuyRequest($buyRequest, $params)
    {
        if (is_array($buyRequest)) {
            $buyRequest = new Varien_Object($buyRequest);
        }
        if (is_array($params)) {
            $params = new Varien_Object($params);
        }


        // Ensure that currentConfig goes as Varien_Object - for easier work with it later
        $currentConfig = $params->getCurrentConfig();
        if ($currentConfig) {
            if (is_array($currentConfig)) {
                $params->setCurrentConfig(new Varien_Object($currentConfig));
            } else if (!($currentConfig instanceof Varien_Object)) {
                $params->unsCurrentConfig();
            }
        }

        /*
         * Notice that '_processing_params' must always be object to protect processing forged requests
         * where '_processing_params' comes in $buyRequest as array from user input
         */
        $processingParams = $buyRequest->getData('_processing_params');
        if (!$processingParams || !($processingParams instanceof Varien_Object)) {
            $processingParams = new Varien_Object();
            $buyRequest->setData('_processing_params', $processingParams);
        }
        $processingParams->addData($params->getData());

        return $buyRequest;
    }

    /**
     * Return loaded oggetto instance
     *
     * @param  int|string $oggettoId (SKU or ID)
     * @param  int $store
     * @param  string $identifierType
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto($oggettoId, $store, $identifierType = null)
    {
        /** @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $oggetto = Mage::getModel('score/oggetto')->setStoreId(Mage::app()->getStore($store)->getId());

        $expectedIdType = false;
        if ($identifierType === null) {
            if (is_string($oggettoId) && !preg_match("/^[+-]?[1-9][0-9]*$|^0$/", $oggettoId)) {
                $expectedIdType = 'sku';
            }
        }

        if ($identifierType == 'sku' || $expectedIdType == 'sku') {
            $idBySku = $oggetto->getIdBySku($oggettoId);
            if ($idBySku) {
                $oggettoId = $idBySku;
            } else if ($identifierType == 'sku') {
                // Return empty oggetto because it was not found by originally specified SKU identifier
                return $oggetto;
            }
        }

        if ($oggettoId && is_numeric($oggettoId)) {
            $oggetto->load((int)$oggettoId);
        }

        return $oggetto;
    }

    /**
     * Set flag that shows if Magento has to check oggetto to be saleable (enabled and/or inStock)
     *
     * For instance, during order creation in the backend admin has ability to add any oggettos to order
     *
     * @param bool $skipSaleableCheck
     * @return Shaurmalab_Score_Helper_Oggetto
     */
    public function setSkipSaleableCheck($skipSaleableCheck = false)
    {
        $this->_skipSaleableCheck = $skipSaleableCheck;
        return $this;
    }

    /**
     * Get flag that shows if Magento has to check oggetto to be saleable (enabled and/or inStock)
     *
     * @return boolean
     */
    public function getSkipSaleableCheck()
    {
        return $this->_skipSaleableCheck;
    }

    public function prepareUrlKey($url_key) {
        $url_key = trim(str_replace(array('/', 'www', 'html', 'php'), '-', $url_key), '-'); // TODO: replace all bad chars with '-' by regexp
        $url_key = trim(str_replace('.', '', $url_key));
        $url_key = preg_replace("/[\/_|+ -]+/", '-', $url_key);
        $url_key = str_replace('--', '-', $url_key);
        $url_key = Mage::helper('score/oggetto_url')->format($url_key);
        
        return $url_key;
    }

    public function modifyParamsAddDefaults($oggettoData, $id = false)
    {

        if(isset($oggettoData['name']) && trim($oggettoData['name']) != '') {
            $url_key = $oggettoData['name'];
            $url_key = $this->prepareUrlKey($url_key);
            $oggettoData['url_key'] = $url_key;
        } elseif(isset($oggettoData['title']) && trim($oggettoData['title']) != '') {
            $url_key = $oggettoData['title'];
            $url_key = $this->prepareUrlKey($url_key);
            $oggettoData['url_key'] = $url_key;
        } else if ((!isset($oggettoData['name']) || trim($oggettoData['name']) == '') && isset($oggettoData['pined_url'])) {
            $arr = explode('//', $oggettoData['pined_url']); // TODO: get url of pined page by regexp
            $url_key = $arr[1];
            $url_key = $this->prepareUrlKey($url_key);
            $oggettoData['url_key'] = $url_key;
        }

        foreach ($oggettoData as $k => $v) {
            if (is_array($v) && count($v) == 1) {
                $oggettoData[$k] = $v[0];
            }
        }
       if(!$id) { 
 
            if (!isset($oggettoData['is_public'])) $oggettoData['is_public'] = 1;

            if (!isset($oggettoData['sku'])) {
                $oggettoData['sku'] = md5(urlencode(@$oggettoData['name'] . @$oggettoData['title'] . @$oggettoData['set'] . @$oggettoData['attribute_set_id']) . date('Y-m-d-h-i-s.u'));
            }

            $oggettoData['status'] = 1;

            if(Mage::getSingleton('customer/session')->getCustomer()->getId()) {
                $oggettoData['owner'] = Mage::getSingleton('customer/session')->getCustomer()->getId();
            } else { 
                $oggettoData['owner'] = 0; 
            }
            
            if (!isset($oggettoData['visibility']) || !$oggettoData['visibility']) $oggettoData['visibility'] = 4; // TODO: make visibility dynamic


            if (!isset($oggettoData['website_ids'])) {
                $oggettoData['website_ids'] = array(0, Mage::app()->getStore()->getWebsiteId());
            }

            if (!isset($oggettoData['type_id'])) {
                $oggettoData['type_id'] = 'simple';
            }
        }
 
        return $oggettoData;

    }

    public function resetOggetto($oggettoId = false)
    {
        $oggetto = Mage::getModel('score/oggetto')->setStoreId(Mage::app()->getStore()->getId());

        if (!$oggettoId) {
            if ($setId = (int)Mage::app()->getRequest()->getParam('set')) {
                $oggetto->setAttributeSetId($setId);
            }

            $oggetto->setTypeId('simple');
        }
        return $oggetto;
    }


    public function isRelatedAttribute($code)
    {

        if (Mage::registry('attribute_sets')) {
            $attributeSets = unserialize(Mage::registry('attribute_sets'));
        } else {
            $attributeSets = Mage::getModel('score/config')->loadAttributeSets()->attributeSetsById;
            Mage::register('attribute_sets', serialize($attributeSets));
        }

        foreach ($attributeSets as $k => $set) {
            $attributeSets[$k] = str_replace(' ', '', strtolower($set));
        }

        $codeParts = explode('_', $code);
        $code = strtolower($codeParts[0]);
        if (isset($codeParts[1]) && $codeParts[1] == 'id' && array_search($code, $attributeSets)) {
            $id = array_search($code, $attributeSets);
            return $id;
        } else {
            return false;
        }
    }

    public function isNumberAttribute($code)
    {

        if (Mage::registry('attribute_sets')) {
            $attributeSets = unserialize(Mage::registry('attribute_sets'));
        } else {
            $attributeSets = Mage::getModel('score/config')->loadAttributeSets()->attributeSetsById;
            Mage::register('attribute_sets', serialize($attributeSets));
        }

        foreach ($attributeSets as $k => $set) {
            $attributeSets[$k] = str_replace(' ', '', strtolower($set));
        }

        $codeParts = explode('_', $code);
        $code = strtolower($codeParts[0]);

        if ($codeParts[count($codeParts) - 1] == 'num' && (array_search($code, $attributeSets) || $codeParts[1] == 'uid')) {
            return true;
        } else {
            return false;
        }
    }

    public function isChainAttribute($code)
    {
        if (Mage::registry('attribute_sets')) {
            $attributeSets = unserialize(Mage::registry('attribute_sets'));
        } else {
            $attributeSets = Mage::getModel('score/config')->loadAttributeSets()->attributeSetsById;
            Mage::register('attribute_sets', serialize($attributeSets));
        }
        foreach ($attributeSets as $k => $set) {
            $attributeSets[$k] = str_replace(' ', '', strtolower($set));
        }

        $codeParts = explode('_', $code);

        if (count($codeParts) == 4) {
            $code1 = strtolower($codeParts[0]);
            $code2 = strtolower($codeParts[2]);
            $id1 = array_search($code1, $attributeSets);
            $id2 = array_search($code2, $attributeSets);
            if ($id1 && $id2) {
                return array($id1, $code2 . '_id');
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function isUserAttribute($code)
    {

        $codeParts = explode('_', $code);
        if ($id = array_search('uid', $codeParts)) {
            return true;
        } else {
            return false;
        }
    }

    public function isDictionaryAttribute($code) { 
        $codeParts = explode('_', $code);
        if ($id = array_search('dict', $codeParts)) {
            return str_replace('_dict','',$code);
        } else {
            return false;
        }
    }

    public function isCounterAttribute($code, $set)
    {

        $codeParts = explode('_', $code);
        if ($id = array_search('counter', $codeParts)) {
            $id = $codeParts[0];
            switch ($id) {
                case 'user':
                    return $id;
                    break;
                default:
                    return false;
                    break;
            }
        } else {
            return false;
        }
    }

    public function getAvailableGroups()
    {
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt' => 0))
            ->addFieldToFilter('store_ids', array(array('like' => $storeId), array('like' => '%,' . $storeId), array('like' => $storeId . ',%'), array('like' => '%,' . $storeId . ',%')));
//        echo $collection->getSelect(); die;
        return $collection;
    }

    public function getAvailableGroupsIerarchy()
    {
        $groups = $this->getAvailableGroups();
        $ierarchy = array();
        foreach ($groups as $group) {
            $ierarchy[] = array('id' => $group->getId(), 'name' => $group->getData('customer_group_code'), 'parent_id' => $group->getParentId());
            $ids[] = $group->getId();
        }

        return $this->generateTree($ierarchy, min($ids));
    }

    public function  generateTree($data, $parent = 0, $depth = 0)
    {
        if ($depth > 5) return ''; // Make sure not to have an endless recursion
        $tree = array();
        for ($i = 0; $i < count($data); $i++) {
            // echo $data[$i]['parent_id'] .'=='. $parent . "<br/>";
            if ($data[$i]['parent_id'] == $parent || $depth == 0) {
                $tree[$data[$i]['id']]['name'] = $data[$i]['name'];
                $tree[$data[$i]['id']]['child'] = $this->generateTree($data, $data[$i]['id'], $depth + 1);
            }
        }
        return $tree;
    }

    public function getAvailableGroupsByNames($groups)
    {
        $groups = explode(',', $groups);
        $groups = array_map("trim", $groups);
        $group_ids = array();
        $availableGroups = $this->getAvailableGroups();
        foreach ($availableGroups as $group) {
            if (in_array($group->getCode(), $groups)) {
                $group_ids[] = $group->getData('customer_group_id');
            }
        }
        $storeId = Mage::app()->getStore()->getId();
        $collection = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('in' => $group_ids))
            ->addFieldToFilter('store_ids', array(array('like' => $storeId), array('like' => '%,' . $storeId), array('like' => $storeId . ',%'), array('like' => '%,' . $storeId . ',%')));

        return $collection;
    }

    public function getUsersCollection($groups)
    {
        $groups = explode(',', $groups);
        $groups = array_map("trim", $groups);
        $group_ids = array();
        $availableGroups = $this->getAvailableGroups();
        foreach ($availableGroups as $group) {
            if (in_array($group->getCode(), $groups)) {
                $group_ids[] = $group->getData('customer_group_id');
            }
        }

        $collection = Mage::getModel('customer/customer')->getCollection();
        if (!empty($group_ids)) $collection->addAttributeToFilter('group_id', array('in' => $group_ids));
        $collection->addNameToSelect()->addAttributeToSelect('*');

        return $collection;
    }

    public function loadAttributeSets()
    {
        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->load();
        $attributeSetsByName = array();
        foreach ($attributeSetCollection as $id => $attributeSet) {
            $name = $attributeSet->getAttributeSetName();
            $attributeSetsByName[strtolower(str_replace(' ', '', $name))] = $id;
        }
        return $attributeSetsByName;
    }

    public function getSetName($oggetto)
    {
        return Mage::getModel('score/config')->getAttributeSetName('score_oggetto', $oggetto->getAttributeSetId());
    }

    public function getSetIdByCode($code)
    {
        $sets = $this->loadAttributeSets();
        if(isset($sets[strtolower(str_replace(' ', '', $code))])) return $sets[strtolower(str_replace(' ', '', $code))];
        return 0;

    }

    public function getSetNameById($id)
    {
        $sets = $this->loadAttributeSets();
        return array_search($id, $sets);
    }

    public function getDictionaryValues($table) { 
        $resource = Mage::getSingleton('core/resource');
        $this->createDictionary($table);
        $readConnection = $resource->getConnection('core_read');
        $elements = $readConnection->query("select * from {$table} where store_id = ".Mage::app()->getStore()->getId())->fetchAll();
        return $elements;
    }

    public function getDictionaryValuesAdmin($table) { 
        $resource = Mage::getSingleton('core/resource');
        $this->createDictionary($table);
        $readConnection = $resource->getConnection('core_read');
        $elements = $readConnection->query("select * from {$table}")->fetchAll();
        return $elements;
    }


    public function createDictionary($table) {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $readConnection->query("
            CREATE TABLE IF NOT EXISTS {$table} (
                id  int(11) NOT NULL AUTO_INCREMENT,
                title VARCHAR(200) NOT NULL DEFAULT '',
                store_id INT(11) NOT NULL,
                PRIMARY KEY (`id`)
            );
            ALTER TABLE {$table} CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;
            ");

    }



}
