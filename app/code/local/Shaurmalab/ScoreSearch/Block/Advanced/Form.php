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
 * @package     Shaurmalab_ScoreSearch
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Advanced search form
 *
 * @category   Mage
 * @package    Shaurmalab_ScoreSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreSearch_Block_Advanced_Form extends Mage_Core_Block_Template
{
    public function _prepareLayout()
    {
        // add Home breadcrumb
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', array(
                'label'=>Mage::helper('scoresearch')->__('Home'),
                'title'=>Mage::helper('scoresearch')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ))->addCrumb('search', array(
                'label'=>Mage::helper('scoresearch')->__('Catalog Advanced Search')
            ));
        }
        return parent::_prepareLayout();
    }

   /**
     * Retrieve collection of product searchable attributes
     *
     * @return Varien_Data_Collection_Db
     */
    public function getSearchableAttributes($setName = '')
    {
        if($setName) { 
             $set = Mage::getResourceModel('eav/entity_attribute_set_collection')
              ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
                ->addFieldToFilter('attribute_set_name',$this->getSet())
                ->getFirstItem(); // TODO: add filter by owner when needed
              $setId = $set->getId();
            /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
            $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();
            $attributes = array();


                /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
                foreach ($groups as $node) {
                        $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                        ->addStoreLabel(Mage::app()->getStore()->getId())
                        ->setAttributeGroupFilter($node->getId())
                        ->addVisibleFilter()
                        ->load();

                    if ($nodeChildren->getSize() > 0) {
                        foreach ($nodeChildren->getItems() as $child) {
                            /* @var $child Mage_Eav_Model_Entity_Attribute */

                            if(($child->getIsVisibleOnFront() || $child->getIsPublic()) && $child->getIsVisibleInAdvancedSearch() && $child->getIsSearchable()) {
                              $attributes[] = $child;
                            }
                        }
                    }
                }
        } 
        else { 
            $attributes = $this->getModel()->getAttributes();
        }
        return $attributes;
    }

    public function getAllAttributes($setName = '')
    {
        if($setName) { 
             $set = Mage::getResourceModel('eav/entity_attribute_set_collection')
              ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
                ->addFieldToFilter('attribute_set_name',$this->getSet())
                ->getFirstItem(); // TODO: add filter by owner when needed
              $setId = $set->getId();
            /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
            $groups = Mage::getModel('eav/entity_attribute_group')
                ->getResourceCollection()
                ->setAttributeSetFilter($setId)
                ->setSortOrder()
                ->load();
            $attributes = array();


                /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
                foreach ($groups as $node) {
                        $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                        ->addStoreLabel(Mage::app()->getStore()->getId())
                        ->setAttributeGroupFilter($node->getId())
                        ->addVisibleFilter()
                        ->load();

                    if ($nodeChildren->getSize() > 0) {
                        foreach ($nodeChildren->getItems() as $child) {
                            /* @var $child Mage_Eav_Model_Entity_Attribute */
                               $attributes[] = $child;
                        }
                    }
                }
        } 
        else { 
            $attributes = $this->getModel()->getAttributes();
        }
        return $attributes;
    }

    /**
     * Retrieve attribute label
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeLabel($attribute)
    {
        return $attribute->getStoreLabel();
    }

    /**
     * Retrieve attribute input validation class
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeValidationClass($attribute)
    {
        return $attribute->getFrontendClass();
    }

    /**
     * Retrieve search string for given field from request
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string|null $part
     * @return mixed|string
     */
    public function getAttributeValue($attribute, $part = null)
    {
        $value = $this->getRequest()->getQuery($attribute->getAttributeCode());
        if ($part && $value) {
            if (isset($value[$part])) {
                $value = $value[$part];
            } else {
                $value = '';
            }
        }

        return $value;
    }

    /**
     * Retrieve the list of available currencies
     *
     * @return array
     */
    public function getAvailableCurrencies()
    {
        $currencies = $this->getData('_currencies');
        if (is_null($currencies)) {
            $currencies = array();
            $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            if (is_array($codes) && count($codes)) {
                $rates = Mage::getModel('directory/currency')->getCurrencyRates(
                    Mage::app()->getStore()->getBaseCurrency(),
                    $codes
                );

                foreach ($codes as $code) {
                    if (isset($rates[$code])) {
                        $currencies[$code] = $code;
                    }
                }
            }

            $this->setData('currencies', $currencies);
        }
        return $currencies;
    }

    /**
     * Count available currencies
     *
     * @return int
     */
    public function getCurrencyCount()
    {
        return count($this->getAvailableCurrencies());
    }

    /**
     * Retrieve currency code for attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getCurrency($attribute)
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();

        $baseCurrency = Mage::app()->getStore()->getBaseCurrency()->getCurrencyCode();
        return $this->getAttributeValue($attribute, 'currency') ?
            $this->getAttributeValue($attribute, 'currency') : $baseCurrency;
    }

    /**
     * Retrieve attribute input type
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return  string
     */
    public function getAttributeInputType($attribute)
    {
        $dataType   = $attribute->getBackend()->getType();
        $imputType  = $attribute->getFrontend()->getInputType();
        if ($imputType == 'select' || $imputType == 'multiselect') {
            return 'select';
        }

        if ($imputType == 'boolean') {
            return 'yesno';
        }

        if ($imputType == 'price') {
            return 'price';
        }

        if ($dataType == 'int' || $dataType == 'decimal') {
            return 'number';
        }

        if ($dataType == 'datetime') {
            return 'date';
        }

        return 'string';
    }

    /**
     * Build attribute select element html string
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeSelectElement($attribute)
    {
        $extra = '';
        $options = $attribute->getSource()->getAllOptions(false);

        $name = $attribute->getAttributeCode();

        // 2 - avoid yes/no selects to be multiselects
        if (is_array($options) && count($options)>2) {
            $extra = 'multiple="multiple" size="4"';
            $name.= '[]';
        }
        else {
            array_unshift($options, array('value'=>'', 'label'=>Mage::helper('scoresearch')->__('All')));
        }

        return $this->_getSelectBlock()
            ->setName($name)
            ->setId($attribute->getAttributeCode())
            ->setTitle($this->getAttributeLabel($attribute))
            ->setExtraParams($extra)
            ->setValue($this->getAttributeValue($attribute))
            ->setOptions($options)
            ->setClass('multiselect')
            ->getHtml();
    }

     /**
     * Build attribute select element html string
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeCheckboxesElement($attribute)
    {
        $extra = '';
        $options = $attribute->getSource()->getAllOptions(false);

        $name = $attribute->getAttributeCode();

        // 2 - avoid yes/no selects to be multiselects
        if (is_array($options) && count($options)>2) {
            $extra = 'multiple="multiple" size="4"';
            $name.= '[]';
        }
        else {
            array_unshift($options, array('value'=>'', 'label'=>Mage::helper('scoresearch')->__('All')));
        }

        $html = '';
        $selectedValue = $this->getAttributeValue($attribute);
        foreach ($options as $key => $value) {
            $checked = '';
            $label = $value['label'];
            $value = $value['value'];
            if(is_string($selectedValue) && $selectedValue == $value) $checked = "checked";
            if(is_array($selectedValue) && in_array($value, $selectedValue)) $checked = "checked";

            $html.="
                <div class='col-md-3'><input type='checkbox' {$checked} name='{$attribute->getAttributeCode()}[]' value='{$value}' id='{$attribute->getAttributeCode()}-{$value}' class='mright5' /> <label for='{$attribute->getAttributeCode()}-{$value}'>{$label}</label></div>
            ";
        }
        return $html;
    }

    /**
     * Retrieve yes/no element html for provided attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return string
     */
    public function getAttributeYesNoElement($attribute)
    {
        $options = array(
            array('value' => '',  'label' => Mage::helper('scoresearch')->__('All')),
            array('value' => '1', 'label' => Mage::helper('scoresearch')->__('Yes')),
            array('value' => '0', 'label' => Mage::helper('scoresearch')->__('No'))
        );

        $name = $attribute->getAttributeCode();
        return $this->_getSelectBlock()
            ->setName($name)
            ->setId($attribute->getAttributeCode())
            ->setTitle($this->getAttributeLabel($attribute))
            ->setExtraParams("")
            ->setValue($this->getAttributeValue($attribute))
            ->setOptions($options)
            ->getHtml();
    }

    protected function _getSelectBlock()
    {
        $block = $this->getData('_select_block');
        if (is_null($block)) {
            $block = $this->getLayout()->createBlock('core/html_select');
            $this->setData('_select_block', $block);
        }
        return $block;
    }

    protected function _getDateBlock()
    {
        $block = $this->getData('_date_block');
        if (is_null($block)) {
            $block = $this->getLayout()->createBlock('core/html_date');
            $this->setData('_date_block', $block);
        }
        return $block;
    }

    /**
     * Retrieve advanced search model object
     *
     * @return Shaurmalab_ScoreSearch_Model_Advanced
     */
    public function getModel()
    {
        return Mage::getSingleton('scoresearch/advanced');
    }

    /**
     * Retrieve search form action url
     *
     * @return string
     */
    public function getSearchPostUrl()
    {
        return $this->getUrl('*/*/result');
    }

    /**
     * Build date element html string for attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param string $part
     * @return string
     */
    public function getDateInput($attribute, $part = 'from')
    {
        $name = $attribute->getAttributeCode() . '[' . $part . ']';
        $value = $this->getAttributeValue($attribute, $part);
        if(!$value) { 
            $currentTimestamp = Mage::getModel('core/date')->gmtTimestamp(time()); //Magento's timestamp function makes a usage of timezone and converts it to timestamp
            $value = Mage::helper('core')->formatDate(date('Y-m-d', $currentTimestamp),'long',false);
        }

        return $this->_getDateBlock()
            ->setName($name)
            ->setId($attribute->getAttributeCode() . ($part == 'from' ? '' : '_' . $part))
            ->setTitle($this->getAttributeLabel($attribute))
            ->setValue($value)
            ->setImage($this->getSkinUrl('images/calendar.gif'))
            ->setFormat(Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_LONG))
            ->setClass('input-text')
            ->getHtml();
    }
}
