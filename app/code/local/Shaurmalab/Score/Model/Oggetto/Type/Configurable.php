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
 * Configurable oggetto type implementation
 *
 * This type builds in oggetto attributes and existing simple oggettos
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Oggetto_Type_Configurable extends Shaurmalab_Score_Model_Oggetto_Type_Abstract
{
    const TYPE_CODE = 'configurable';

    /**
     * Cache key for Used Oggetto Attribute Ids
     *
     * @var string
     */
    protected $_usedOggettoAttributeIds = '_cache_instance_used_oggetto_attribute_ids';

    /**
     * Cache key for Used Oggetto Attributes
     *
     * @var string
     */
    protected $_usedOggettoAttributes   = '_cache_instance_used_oggetto_attributes';

    /**
     * Cache key for Used Attributes
     *
     * @var string
     */
    protected $_usedAttributes          = '_cache_instance_used_attributes';

    /**
     * Cache key for configurable attributes
     *
     * @var string
     */
    protected $_configurableAttributes  = '_cache_instance_configurable_attributes';

    /**
     * Cache key for Used oggetto ids
     *
     * @var string
     */
    protected $_usedOggettoIds          = '_cache_instance_oggetto_ids';

    /**
     * Cache key for used oggettos
     *
     * @var string
     */
    protected $_usedOggettos            = '_cache_instance_oggettos';

    /**
     * Oggetto is composite
     *
     * @var bool
     */
    protected $_isComposite             = true;

    /**
     * Oggetto is configurable
     *
     * @var bool
     */
    protected $_canConfigure            = true;

    /**
     * Return relation info about used oggettos
     *
     * @return Varien_Object Object with information data
     */
    public function getRelationInfo()
    {
        $info = new Varien_Object();
        $info->setTable('score/oggetto_super_link')
            ->setParentFieldName('parent_id')
            ->setChildFieldName('oggetto_id');
        return $info;
    }

    /**
     * Retrieve Required children ids
     * Return grouped array, ex array(
     *   group => array(ids)
     * )
     *
     * @param  int $parentId
     * @param  bool $required
     * @return array
     */
    public function getChildrenIds($parentId, $required = true)
    {
        return Mage::getResourceSingleton('score/oggetto_type_configurable')
            ->getChildrenIds($parentId, $required);
    }

    /**
     * Retrieve parent ids array by required child
     *
     * @param  int|array $childId
     * @return array
     */
    public function getParentIdsByChild($childId)
    {
        return Mage::getResourceSingleton('score/oggetto_type_configurable')
            ->getParentIdsByChild($childId);
    }

    /**
     * Retrieve oggetto type attributes
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getEditableAttributes($oggetto = null)
    {
        if (is_null($this->_editableAttributes)) {
            $this->_editableAttributes = parent::getEditableAttributes($oggetto);
            foreach ($this->_editableAttributes as $index => $attribute) {
                if ($this->getUsedOggettoAttributeIds($oggetto)
                    && in_array($attribute->getAttributeId(), $this->getUsedOggettoAttributeIds($oggetto))) {
                    unset($this->_editableAttributes[$index]);
                }
            }
        }
        return $this->_editableAttributes;
    }

    /**
     * Checkin attribute availability for create superoggetto
     *
     * @param   Mage_Eav_Model_Entity_Attribute $attribute
     * @return  bool
     */
    public function canUseAttribute(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        $allow = $attribute->getIsGlobal() == Shaurmalab_Score_Model_Resource_Eav_Attribute::SCOPE_GLOBAL
            && $attribute->getIsVisible()
            && $attribute->getIsConfigurable()
            && $attribute->usesSource()
            && $attribute->getIsUserDefined();

        return $allow;
    }

    /**
     * Declare attribute identifiers used for assign suboggettos
     *
     * @param   array $ids
     * @param   Shaurmalab_Score_Model_Oggetto $oggetto
     * @return  Shaurmalab_Score_Model_Oggetto_Type_Configurable
     */
    public function setUsedOggettoAttributeIds($ids, $oggetto = null)
    {
        $usedOggettoAttributes  = array();
        $configurableAttributes = array();

        foreach ($ids as $attributeId) {
            $usedOggettoAttributes[]  = $this->getAttributeById($attributeId);
            $configurableAttributes[] = Mage::getModel('score/oggetto_type_configurable_attribute')
                ->setOggettoAttribute($this->getAttributeById($attributeId));
        }
        $this->getOggetto($oggetto)->setData($this->_usedOggettoAttributes, $usedOggettoAttributes);
        $this->getOggetto($oggetto)->setData($this->_usedOggettoAttributeIds, $ids);
        $this->getOggetto($oggetto)->setData($this->_configurableAttributes, $configurableAttributes);

        return $this;
    }

    /**
     * Retrieve identifiers of used oggetto attributes
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getUsedOggettoAttributeIds($oggetto = null)
    {
        if (!$this->getOggetto($oggetto)->hasData($this->_usedOggettoAttributeIds)) {
            $usedOggettoAttributeIds = array();
            foreach ($this->getUsedOggettoAttributes($oggetto) as $attribute) {
                $usedOggettoAttributeIds[] = $attribute->getId();
            }
            $this->getOggetto($oggetto)->setData($this->_usedOggettoAttributeIds, $usedOggettoAttributeIds);
        }
        return $this->getOggetto($oggetto)->getData($this->_usedOggettoAttributeIds);
    }

    /**
     * Retrieve used oggetto attributes
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getUsedOggettoAttributes($oggetto = null)
    {
        if (!$this->getOggetto($oggetto)->hasData($this->_usedOggettoAttributes)) {
            $usedOggettoAttributes = array();
            $usedAttributes        = array();
            foreach ($this->getConfigurableAttributes($oggetto) as $attribute) {
                if (!is_null($attribute->getOggettoAttribute())) {
                    $id = $attribute->getOggettoAttribute()->getId();
                    $usedOggettoAttributes[$id] = $attribute->getOggettoAttribute();
                    $usedAttributes[$id]        = $attribute;
                }
            }
            $this->getOggetto($oggetto)->setData($this->_usedAttributes, $usedAttributes);
            $this->getOggetto($oggetto)->setData($this->_usedOggettoAttributes, $usedOggettoAttributes);
        }
        return $this->getOggetto($oggetto)->getData($this->_usedOggettoAttributes);
    }

    /**
     * Retrieve configurable attributes data
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getConfigurableAttributes($oggetto = null)
    {
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if (!$this->getOggetto($oggetto)->hasData($this->_configurableAttributes)) {
            $configurableAttributes = $this->getConfigurableAttributeCollection($oggetto)
                ->orderByPosition()
                ->load();
            $this->getOggetto($oggetto)->setData($this->_configurableAttributes, $configurableAttributes);
        }
        Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
        return $this->getOggetto($oggetto)->getData($this->_configurableAttributes);
    }

    /**
     * Retrieve Configurable Attributes as array
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getConfigurableAttributesAsArray($oggetto = null)
    {
        $res = array();
        foreach ($this->getConfigurableAttributes($oggetto) as $attribute) {
            $res[] = array(
                'id'             => $attribute->getId(),
                'label'          => $attribute->getLabel(),
                'use_default'    => $attribute->getUseDefault(),
                'position'       => $attribute->getPosition(),
                'values'         => $attribute->getPrices() ? $attribute->getPrices() : array(),
                'attribute_id'   => $attribute->getOggettoAttribute()->getId(),
                'attribute_code' => $attribute->getOggettoAttribute()->getAttributeCode(),
                'frontend_label' => $attribute->getOggettoAttribute()->getFrontend()->getLabel(),
                'store_label'    => $attribute->getOggettoAttribute()->getStoreLabel(),
            );
        }
        return $res;
    }

    /**
     * Retrieve configurable attribute collection
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Type_Configurable_Attribute_Collection
     */
    public function getConfigurableAttributeCollection($oggetto = null)
    {
        return Mage::getResourceModel('score/oggetto_type_configurable_attribute_collection')
            ->setOggettoFilter($this->getOggetto($oggetto));
    }


    /**
     * Retrieve suboggettos identifiers
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getUsedOggettoIds($oggetto = null)
    {
        if (!$this->getOggetto($oggetto)->hasData($this->_usedOggettoIds)) {
            $usedOggettoIds = array();
            foreach ($this->getUsedOggettos(null, $oggetto) as $oggetto) {
                $usedOggettoIds[] = $oggetto->getId();
            }
            $this->getOggetto($oggetto)->setData($this->_usedOggettoIds, $usedOggettoIds);
        }
        return $this->getOggetto($oggetto)->getData($this->_usedOggettoIds);
    }

    /**
     * Retrieve array of "suboggettos"
     *
     * @param  array
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getUsedOggettos($requiredAttributeIds = null, $oggetto = null)
    {
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if (!$this->getOggetto($oggetto)->hasData($this->_usedOggettos)) {
            if (is_null($requiredAttributeIds)
                and is_null($this->getOggetto($oggetto)->getData($this->_configurableAttributes))) {
                // If used oggettos load before attributes, we will load attributes.
                $this->getConfigurableAttributes($oggetto);
                // After attributes loading oggettos loaded too.
                Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
                return $this->getOggetto($oggetto)->getData($this->_usedOggettos);
            }

            $usedOggettos = array();
            $collection = $this->getUsedOggettoCollection($oggetto)
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions();

            if (is_array($requiredAttributeIds)) {
                foreach ($requiredAttributeIds as $attributeId) {
                    $attribute = $this->getAttributeById($attributeId, $oggetto);
                    if (!is_null($attribute))
                        $collection->addAttributeToFilter($attribute->getAttributeCode(), array('notnull'=>1));
                }
            }

            foreach ($collection as $item) {
                $usedOggettos[] = $item;
            }

            $this->getOggetto($oggetto)->setData($this->_usedOggettos, $usedOggettos);
        }
        Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
        return $this->getOggetto($oggetto)->getData($this->_usedOggettos);
    }

    /**
     * Retrieve related oggettos collection
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Eav_Mysql4_Oggetto_Type_Configurable_Oggetto_Collection
     */
    public function getUsedOggettoCollection($oggetto = null)
    {
        $collection = Mage::getResourceModel('score/oggetto_type_configurable_oggetto_collection')
            ->setFlag('require_stock_items', true)
            ->setFlag('oggetto_children', true)
            ->setOggettoFilter($this->getOggetto($oggetto));
        if (!is_null($this->getStoreFilter($oggetto))) {
            $collection->addStoreFilter($this->getStoreFilter($oggetto));
        }

        return $collection;
    }

    /**
     * Before save process
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Type_Configurable
     */
    public function beforeSave($oggetto = null)
    {
        parent::beforeSave($oggetto);

        $this->getOggetto($oggetto)->canAffectOptions(false);

        if ($this->getOggetto($oggetto)->getCanSaveConfigurableAttributes()) {
            $this->getOggetto($oggetto)->canAffectOptions(true);
            $data = $this->getOggetto($oggetto)->getConfigurableAttributesData();
            if (!empty($data)) {
                foreach ($data as $attribute) {
                    if (!empty($attribute['values'])) {
                        $this->getOggetto($oggetto)->setTypeHasOptions(true);
                        $this->getOggetto($oggetto)->setTypeHasRequiredOptions(true);
                        break;
                    }
                }
            }
        }
        foreach ($this->getConfigurableAttributes($oggetto) as $attribute) {
            $this->getOggetto($oggetto)->setData($attribute->getOggettoAttribute()->getAttributeCode(), null);
        }

        return $this;
    }

    /**
     * Save configurable oggetto depended data
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Type_Configurable
     */
    public function save($oggetto = null)
    {
        parent::save($oggetto);
        /**
         * Save Attributes Information
         */
        if ($data = $this->getOggetto($oggetto)->getConfigurableAttributesData()) {
            foreach ($data as $attributeData) {
                $id = isset($attributeData['id']) ? $attributeData['id'] : null;
                Mage::getModel('score/oggetto_type_configurable_attribute')
                   ->setData($attributeData)
                   ->setId($id)
                   ->setStoreId($this->getOggetto($oggetto)->getStoreId())
                   ->setOggettoId($this->getOggetto($oggetto)->getId())
                   ->save();
            }
        }

        /**
         * Save oggetto relations
         */
        $data = $this->getOggetto($oggetto)->getConfigurableOggettosData();
        if (is_array($data)) {
            $oggettoIds = array_keys($data);
            Mage::getResourceModel('score/oggetto_type_configurable')
                ->saveOggettos($this->getOggetto($oggetto), $oggettoIds);
        }
        return $this;
    }

    /**
     * Check is oggetto available for sale
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function isSalable($oggetto = null)
    {
        $salable = parent::isSalable($oggetto);

        if ($salable !== false) {
            $salable = false;
            if (!is_null($oggetto)) {
                $this->setStoreFilter($oggetto->getStoreId(), $oggetto);
            }
            foreach ($this->getUsedOggettos(null, $oggetto) as $child) {
                if ($child->isSalable()) {
                    $salable = true;
                    break;
                }
            }
        }

        return $salable;
    }

    /**
     * Check whether the oggetto is available for sale
     * is alias to isSalable for compatibility
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function getIsSalable($oggetto = null)
    {
        return $this->isSalable($oggetto);
    }

    /**
     * Retrieve used oggetto by attribute values
     *  $attrbutesInfo = array(
     *      $attributeId => $attributeValue
     *  )
     *
     * @param  array $attributesInfo
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto|null
     */
    public function getOggettoByAttributes($attributesInfo, $oggetto = null)
    {
        if (is_array($attributesInfo) && !empty($attributesInfo)) {
            $oggettoCollection = $this->getUsedOggettoCollection($oggetto)->addAttributeToSelect('name');
            foreach ($attributesInfo as $attributeId => $attributeValue) {
                $oggettoCollection->addAttributeToFilter($attributeId, $attributeValue);
            }
            $oggettoObject = $oggettoCollection->getFirstItem();
            if ($oggettoObject->getId()) {
                return $oggettoObject;
            }

            foreach ($this->getUsedOggettos(null, $oggetto) as $oggettoObject) {
                $checkRes = true;
                foreach ($attributesInfo as $attributeId => $attributeValue) {
                    $code = $this->getAttributeById($attributeId, $oggetto)->getAttributeCode();
                    if ($oggettoObject->getData($code) != $attributeValue) {
                        $checkRes = false;
                    }
                }
                if ($checkRes) {
                    return $oggettoObject;
                }
            }
        }
        return null;
    }

    /**
     * Retrieve Selected Attributes info
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getSelectedAttributesInfo($oggetto = null)
    {
        $attributes = array();
        Varien_Profiler::start('CONFIGURABLE:'.__METHOD__);
        if ($attributesOption = $this->getOggetto($oggetto)->getCustomOption('attributes')) {
            $data = unserialize($attributesOption->getValue());
            $this->getUsedOggettoAttributeIds($oggetto);

            $usedAttributes = $this->getOggetto($oggetto)->getData($this->_usedAttributes);

            foreach ($data as $attributeId => $attributeValue) {
                if (isset($usedAttributes[$attributeId])) {
                    $attribute = $usedAttributes[$attributeId];
                    $label = $attribute->getLabel();
                    $value = $attribute->getOggettoAttribute();
                    if ($value->getSourceModel()) {
                        $value = $value->getSource()->getOptionText($attributeValue);
                    }
                    else {
                        $value = '';
                    }

                    $attributes[] = array('label'=>$label, 'value'=>$value);
                }
            }
        }
        Varien_Profiler::stop('CONFIGURABLE:'.__METHOD__);
        return $attributes;
    }

    /**
     * Prepare oggetto and its configuration to be added to some oggettos list.
     * Perform standard preparation process and then add Configurable specific options.
     *
     * @param Varien_Object $buyRequest
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param string $processMode
     * @return array|string
     */
    protected function _prepareOggetto(Varien_Object $buyRequest, $oggetto, $processMode)
    {
        $attributes = $buyRequest->getSuperAttribute();
        if ($attributes || !$this->_isStrictProcessMode($processMode)) {
            if (!$this->_isStrictProcessMode($processMode)) {
                if (is_array($attributes)) {
                    foreach ($attributes as $key => $val) {
                        if (empty($val)) {
                            unset($attributes[$key]);
                        }
                    }
                } else {
                    $attributes = array();
                }
            }

            $result = parent::_prepareOggetto($buyRequest, $oggetto, $processMode);
            if (is_array($result)) {
                $oggetto = $this->getOggetto($oggetto);
                /**
                 * $attributes = array($attributeId=>$attributeValue)
                 */
                $subOggetto = true;
                if ($this->_isStrictProcessMode($processMode)) {
                    foreach($this->getConfigurableAttributes($oggetto) as $attributeItem){
                        /* @var $attributeItem Varien_Object */
                        $attrId = $attributeItem->getData('attribute_id');
                        if(!isset($attributes[$attrId]) || empty($attributes[$attrId])) {
                            $subOggetto = null;
                            break;
                        }
                    }
                }
                if( $subOggetto ) {
                    $subOggetto = $this->getOggettoByAttributes($attributes, $oggetto);
                }

                if ($subOggetto) {
                    $oggetto->addCustomOption('attributes', serialize($attributes));
                    $oggetto->addCustomOption('oggetto_qty_'.$subOggetto->getId(), 1, $subOggetto);
                    $oggetto->addCustomOption('simple_oggetto', $subOggetto->getId(), $subOggetto);

                    $_result = $subOggetto->getTypeInstance(true)->_prepareOggetto(
                        $buyRequest,
                        $subOggetto,
                        $processMode
                    );
                    if (is_string($_result) && !is_array($_result)) {
                        return $_result;
                    }

                    if (!isset($_result[0])) {
                        return Mage::helper('checkout')->__('Cannot add the item to shopping cart');
                    }

                    /**
                     * Adding parent oggetto custom options to child oggetto
                     * to be sure that it will be unique as its parent
                     */
                    if ($optionIds = $oggetto->getCustomOption('option_ids')) {
                        $optionIds = explode(',', $optionIds->getValue());
                        foreach ($optionIds as $optionId) {
                            if ($option = $oggetto->getCustomOption('option_' . $optionId)) {
                                $_result[0]->addCustomOption('option_' . $optionId, $option->getValue());
                            }
                        }
                    }

                    $_result[0]->setParentOggettoId($oggetto->getId())
                        // add custom option to simple oggetto for protection of process
                        //when we add simple oggetto separately
                        ->addCustomOption('parent_oggetto_id', $oggetto->getId());
                    if ($this->_isStrictProcessMode($processMode)) {
                        $_result[0]->setCartQty(1);
                    }
                    $result[] = $_result[0];
                    return $result;
                } else if (!$this->_isStrictProcessMode($processMode)) {
                    return $result;
                }
            }
        }

        return $this->getSpecifyOptionMessage();
    }

    /**
     * Check if oggetto can be bought
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Type_Configurable
     * @throws Mage_Core_Exception
     */
    public function checkOggettoBuyState($oggetto = null)
    {
        parent::checkOggettoBuyState($oggetto);
        $oggetto = $this->getOggetto($oggetto);
        $option = $oggetto->getCustomOption('info_buyRequest');
        if ($option instanceof Mage_Sales_Model_Quote_Item_Option) {
            $buyRequest = new Varien_Object(unserialize($option->getValue()));
            $attributes = $buyRequest->getSuperAttribute();
            if (is_array($attributes)) {
                foreach ($attributes as $key => $val) {
                    if (empty($val)) {
                        unset($attributes[$key]);
                    }
                }
            }
            if (empty($attributes)) {
                Mage::throwException($this->getSpecifyOptionMessage());
            }
        }
        return $this;
    }

    /**
     * Retrieve message for specify option(s)
     *
     * @return string
     */
    public function getSpecifyOptionMessage()
    {
        return Mage::helper('score')->__('Please specify the oggetto\'s option(s).');
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this oggetto
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getOrderOptions($oggetto = null)
    {
        $options = parent::getOrderOptions($oggetto);
        $options['attributes_info'] = $this->getSelectedAttributesInfo($oggetto);
        if ($simpleOption = $this->getOggetto($oggetto)->getCustomOption('simple_oggetto')) {
            $options['simple_name'] = $simpleOption->getOggetto($oggetto)->getName();
            $options['simple_sku']  = $simpleOption->getOggetto($oggetto)->getSku();
        }

        $options['oggetto_calculations'] = self::CALCULATE_PARENT;
        $options['shipment_type'] = self::SHIPMENT_TOGETHER;

        return $options;
    }

    /**
     * Check is virtual oggetto
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function isVirtual($oggetto = null)
    {
        if ($oggettoOption = $this->getOggetto($oggetto)->getCustomOption('simple_oggetto')) {
            if ($optionOggetto = $oggettoOption->getOggetto()) {
                /* @var $optionOggetto Shaurmalab_Score_Model_Oggetto */
                return $optionOggetto->isVirtual();
            }
        }
        return parent::isVirtual($oggetto);
    }

    /**
     * Return true if oggetto has options
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return bool
     */
    public function hasOptions($oggetto = null)
    {
        if ($this->getOggetto($oggetto)->getOptions()) {
            return true;
        }

        $attributes = $this->getConfigurableAttributes($oggetto);
        if (count($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var Shaurmalab_Score_Model_Oggetto_Type_Configurable_Attribute $attribute */
                if ($attribute->getData('prices')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Return oggetto weight based on simple oggetto
     * weight or configurable oggetto weight
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return decimal
     */
    public function getWeight($oggetto = null)
    {
        if ($this->getOggetto($oggetto)->hasCustomOptions() &&
            ($simpleOggettoOption = $this->getOggetto($oggetto)->getCustomOption('simple_oggetto'))
        ) {
            $simpleOggetto = $simpleOggettoOption->getOggetto($oggetto);
            if ($simpleOggetto) {
                return $simpleOggetto->getWeight();
            }
        }

        return $this->getOggetto($oggetto)->getData('weight');
    }

    /**
     * Implementation of oggetto specify logic of which oggetto needs to be assigned to option.
     * For example if oggetto which was added to option already removed from catalog.
     *
     * @param  Shaurmalab_Score_Model_Oggetto|null $optionOggetto
     * @param  Mage_Sales_Model_Quote_Item_Option $option
     * @param  Shaurmalab_Score_Model_Oggetto|null $oggetto
     * @return Shaurmalab_Score_Model_Oggetto_Type_Configurable
     */
    public function assignOggettoToOption($optionOggetto, $option, $oggetto = null)
    {
        if ($optionOggetto) {
            $option->setOggetto($optionOggetto);
        } else {
            $option->getItem()->setHasConfigurationUnavailableError(true);
        }
        return $this;
    }

    /**
     * Retrieve oggettos divided into groups required to purchase
     * At least one oggetto in each group has to be purchased
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getOggettosToPurchaseByReqGroups($oggetto = null)
    {
        $oggetto = $this->getOggetto($oggetto);
        return array($this->getUsedOggettos(null, $oggetto));
    }

    /**
     * Get sku of oggetto
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @return string
     */
    public function getSku($oggetto = null)
    {
        $simpleOption = $this->getOggetto($oggetto)->getCustomOption('simple_oggetto');
        if($simpleOption) {
            $optionOggetto = $simpleOption->getOggetto($oggetto);
            $simpleSku = null;
            if ($optionOggetto) {
                $simpleSku =  $simpleOption->getOggetto($oggetto)->getSku();
            }
            $sku = parent::getOptionSku($oggetto, $simpleSku);
        } else {
            $sku = parent::getSku($oggetto);
        }

        return $sku;
    }

    /**
     * Prepare selected options for configurable oggetto
     *
     * @param  Shaurmalab_Score_Model_Oggetto $oggetto
     * @param  Varien_Object $buyRequest
     * @return array
     */
    public function processBuyRequest($oggetto, $buyRequest)
    {
        $superAttribute = $buyRequest->getSuperAttribute();
        $superAttribute = (is_array($superAttribute)) ? array_filter($superAttribute, 'intval') : array();

        $options = array('super_attribute' => $superAttribute);

        return $options;
    }

    /**
     * Check if Minimum Advertise Price is enabled at least in one option
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @param int $visibility
     * @return bool|null
     */
    public function isMapEnabledInOptions($oggetto, $visibility = null)
    {
        return null;
    }

    /**
     * Prepare and retrieve options values with oggetto data
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return array
     */
    public function getConfigurableOptions($oggetto)
    {
        return Mage::getResourceSingleton('score/oggetto_type_configurable')
            ->getConfigurableOptions($oggetto, $this->getUsedOggettoAttributes($oggetto));
    }
}
