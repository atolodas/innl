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


class Shaurmalab_Score_Model_Config extends Mage_Eav_Model_Config
{
    const XML_PATH_LIST_DEFAULT_SORT_BY     = 'score/frontend/default_sort_by';

    protected $_attributeSetsById;
    protected $_attributeSetsByName;

    protected $_attributeGroupsById;
    protected $_attributeGroupsByName;

    protected $_oggettoTypesById;

    /**
     * Array of attributes codes needed for oggetto load
     *
     * @var array
     */
    protected $_oggettoAttributes;

    /**
     * Oggetto Attributes used in oggetto listing
     *
     * @var array
     */
    protected $_usedInOggettoListing;

    /**
     * Oggetto Attributes For Sort By
     *
     * @var array
     */
    protected $_usedForSortBy;

    protected $_storeId = null;

    const XML_PATH_OGGETTO_COLLECTION_ATTRIBUTES = 'frontend/oggetto/collection/attributes';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('score/config');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Shaurmalab_Score_Model_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = $storeId;
        return $this;
    }

    /**
     * Return store id, if is not set return current app store
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return Mage::app()->getStore()->getId();
        }
        return $this->_storeId;
    }

    public function loadAttributeSets()
    {
        if ($this->_attributeSetsById) {
            return $this;
        }

        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->load();

        $this->_attributeSetsById = array();
        $this->_attributeSetsByName = array();
        foreach ($attributeSetCollection as $id=>$attributeSet) {
            $entityTypeId = $attributeSet->getEntityTypeId();
            $name = $attributeSet->getAttributeSetName();
            $this->_attributeSetsById[$entityTypeId][$id] = $name;
            $this->attributeSetsById[$id] = $name;
            $this->_attributeSetsByName[$entityTypeId][strtolower($name)] = $id;
        }
        return $this;
    }

    public function getAttributeSetName($entityTypeId, $id)
    {
        if (!is_numeric($id)) {
            return $id;
        }
        $this->loadAttributeSets();

        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId)->getId();
        }

        return isset($this->_attributeSetsById[$entityTypeId][$id]) ? $this->_attributeSetsById[$entityTypeId][$id] : false;
    }

    public function getAttributeSetId($entityTypeId, $name)
    {
        if (is_numeric($name)) {
            return $name;
        }
        $this->loadAttributeSets();

        if (!is_numeric($entityTypeId)) {
            $entityTypeId = $this->getEntityType($entityTypeId)->getId();
        }
        $name = strtolower($name);
        return isset($this->_attributeSetsByName[$entityTypeId][$name]) ? $this->_attributeSetsByName[$entityTypeId][$name] : false;
    }

    public function loadAttributeGroups()
    {
        if ($this->_attributeGroupsById) {
            return $this;
        }

        $attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
            ->load();

        $this->_attributeGroupsById = array();
        $this->_attributeGroupsByName = array();
        foreach ($attributeSetCollection as $id=>$attributeGroup) {
            $attributeSetId = $attributeGroup->getAttributeSetId();
            $name = $attributeGroup->getAttributeGroupName();
            $this->_attributeGroupsById[$attributeSetId][$id] = $name;
            $this->_attributeGroupsByName[$attributeSetId][strtolower($name)] = $id;
        }
        return $this;
    }

    public function getAttributeGroupName($attributeSetId, $id)
    {
        if (!is_numeric($id)) {
            return $id;
        }

        $this->loadAttributeGroups();

        if (!is_numeric($attributeSetId)) {
            $attributeSetId = $this->getAttributeSetId($attributeSetId);
        }
        return isset($this->_attributeGroupsById[$attributeSetId][$id]) ? $this->_attributeGroupsById[$attributeSetId][$id] : false;
    }

    public function getAttributeGroupId($attributeSetId, $name)
    {
        if (is_numeric($name)) {
            return $name;
        }

        $this->loadAttributeGroups();

        if (!is_numeric($attributeSetId)) {
            $attributeSetId = $this->getAttributeSetId($attributeSetId);
        }
        $name = strtolower($name);
        return isset($this->_attributeGroupsByName[$attributeSetId][$name]) ? $this->_attributeGroupsByName[$attributeSetId][$name] : false;
    }

    public function loadOggettoTypes()
    {
        if ($this->_oggettoTypesById) {
            return $this;
        }

        /*
        $oggettoTypeCollection = Mage::getResourceModel('score/oggetto_type_collection')
            ->load();
        */
        $oggettoTypeCollection = Mage::getModel('score/oggetto_type')
            ->getOptionArray();

        $this->_oggettoTypesById = array();
        $this->_oggettoTypesByName = array();
        foreach ($oggettoTypeCollection as $id=>$type) {
            //$name = $type->getCode();
            $name = $type;
            $this->_oggettoTypesById[$id] = $name;
            $this->_oggettoTypesByName[strtolower($name)] = $id;
        }
        return $this;
    }

    public function getOggettoTypeId($name)
    {
        if (is_numeric($name)) {
            return $name;
        }

        $this->loadOggettoTypes();

        $name = strtolower($name);
        return isset($this->_oggettoTypesByName[$name]) ? $this->_oggettoTypesByName[$name] : false;
    }

    public function getOggettoTypeName($id)
    {
        if (!is_numeric($id)) {
            return $id;
        }

        $this->loadOggettoTypes();

        return isset($this->_oggettoTypesById[$id]) ? $this->_oggettoTypesById[$id] : false;
    }

    public function getSourceOptionId($source, $value)
    {
        foreach ($source->getAllOptions() as $option) {
            if (strcasecmp($option['label'], $value)==0 || $option['value'] == $value) {
                return $option['value'];
            }
        }
        return null;
    }

    /**
     * Load Oggetto attributes
     *
     * @return array
     */
    public function getOggettoAttributes()
    {
        if (is_null($this->_oggettoAttributes)) {
            $this->_oggettoAttributes = array_keys($this->getAttributesUsedInOggettoListing());
        }
        return $this->_oggettoAttributes;
    }

    /**
     * Retrieve Oggetto Collection Attributes from XML config file
     * Used only for install/upgrade
     *
     * @return array
     */
    public function getOggettoCollectionAttributes() {
        $attributes = Mage::getConfig()
            ->getNode(self::XML_PATH_OGGETTO_COLLECTION_ATTRIBUTES)
            ->asArray();
        return array_keys($attributes);;
    }

    /**
     * Retrieve resource model
     *
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Config
     */
    protected function _getResource()
    {
        return Mage::getResourceModel('score/config');
    }

    /**
     * Retrieve Attributes used in oggetto listing
     *
     * @return array
     */
    public function getAttributesUsedInOggettoListing() {
        if (is_null($this->_usedInOggettoListing)) {
            $this->_usedInOggettoListing = array();
            $entityType = Shaurmalab_Score_Model_Oggetto::ENTITY;
            $attributesData = $this->_getResource()
                ->setStoreId($this->getStoreId())
                ->getAttributesUsedInListing();
            Mage::getSingleton('eav/config')
                ->importAttributesData($entityType, $attributesData);
            foreach ($attributesData as $attributeData) {
                $attributeCode = $attributeData['attribute_code'];
                $this->_usedInOggettoListing[$attributeCode] = Mage::getSingleton('eav/config')
                    ->getAttribute($entityType, $attributeCode);
            }
        }
        return $this->_usedInOggettoListing;
    }

    /**
     * Retrieve Attributes array used for sort by
     *
     * @return array
     */
    public function getAttributesUsedForSortBy() {
        if (is_null($this->_usedForSortBy)) {
            $this->_usedForSortBy = array();
            $entityType     = Shaurmalab_Score_Model_Oggetto::ENTITY;
            $attributesData = $this->_getResource()
                ->getAttributesUsedForSortBy();
            Mage::getSingleton('eav/config')
                ->importAttributesData($entityType, $attributesData);
            foreach ($attributesData as $attributeData) {
                $attributeCode = $attributeData['attribute_code'];
                $this->_usedForSortBy[$attributeCode] = Mage::getSingleton('eav/config')
                    ->getAttribute($entityType, $attributeCode);
            }
        }
        return $this->_usedForSortBy;
    }

    /**
     * Retrieve Attributes Used for Sort by as array
     * key = code, value = name
     *
     * @return array
     */
    public function getAttributeUsedForSortByArray()
    {
        $options = array(
        //    'position'  => Mage::helper('score')->__('Position')
        );
        foreach ($this->getAttributesUsedForSortBy() as $attribute) {
            /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */
            $options[$attribute->getAttributeCode()] = $attribute->getStoreLabel();
        }

        return $options;
    }

    /**
     * Retrieve Oggetto List Default Sort By
     *
     * @param mixed $store
     * @return string
     */
    public function getOggettoListDefaultSortBy($store = null) {
        return Mage::getStoreConfig(self::XML_PATH_LIST_DEFAULT_SORT_BY, $store);
    }
}
