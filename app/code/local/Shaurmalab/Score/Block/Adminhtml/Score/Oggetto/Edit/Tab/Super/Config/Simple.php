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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quiq simple entity creation
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Config_Simple
    extends Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Attributes
{
    /**
     * Link to currently editing entity
     *
     * @var Shaurmalab_Score_Model_Oggetto
     */
    protected $_entity = null;

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setFieldNameSuffix('simple_entity');
        $form->setDataObject($this->_getOggetto());

        $fieldset = $form->addFieldset('simple_entity', array(
            'legend' => Mage::helper('score')->__('Quick simple entity creation')
        ));
        $this->_addElementTypes($fieldset);
        $attributesConfig = array(
            'autogenerate' => array('name', 'sku'),
            'additional'   => array('name', 'sku', 'visibility', 'status')
        );

        $availableTypes = array('text', 'select', 'multiselect', 'image', 'textarea', 'price', 'weight');

        $attributes = Mage::getModel('score/oggetto')
            ->setTypeId(Shaurmalab_Score_Model_Oggetto_Type::TYPE_SIMPLE)
            ->setAttributeSetId($this->_getOggetto()->getAttributeSetId())
            ->getAttributes();

        /* Standart attributes */
        foreach ($attributes as $attribute) {
            if (
            (
            // $attribute->getIsRequired()
            //     && $attribute->getApplyTo()
                // If not applied to configurable
                //&&
                !in_array(Shaurmalab_Score_Model_Oggetto_Type::TYPE_CONFIGURABLE, $attribute->getApplyTo())
                // If not used in configurable
                && !in_array($attribute->getId(),
                    $this->_getOggetto()->getTypeInstance(true)->getUsedOggettoAttributeIds($this->_getOggetto()))
                )
                // Or in additional
                || in_array($attribute->getAttributeCode(), $attributesConfig['additional'])
            ) {
                $inputType = $attribute->getFrontend()->getInputType();
                if (!in_array($inputType, $availableTypes)) {
                    continue;
                }
                $attributeCode = $attribute->getAttributeCode();
                $attribute->setAttributeCode('simple_entity_' . $attributeCode);
                $element = $fieldset->addField(
                    'simple_entity_' . $attributeCode,
                     $inputType,
                     array(
                        'label'    => $attribute->getFrontend()->getLabel(),
                        'name'     => $attributeCode,
                        'required' => $attribute->getIsRequired(),
                     )
                )->setEntityAttribute($attribute);

                if (in_array($attributeCode, $attributesConfig['autogenerate'])) {
                    $element->setDisabled('true');
                    $element->setValue($this->_getOggetto()->getData($attributeCode));
                    $element->setAfterElementHtml(
                         '<input type="checkbox" id="simple_entity_' . $attributeCode . '_autogenerate" '
                         . 'name="simple_entity[' . $attributeCode . '_autogenerate]" value="1" '
                         . 'onclick="toggleValueElements(this, this.parentNode)" checked="checked" /> '
                         . '<label for="simple_entity_' . $attributeCode . '_autogenerate" >'
                         . Mage::helper('score')->__('Autogenerate')
                         . '</label>'
                    );
                }


                if ($inputType == 'select' || $inputType == 'multiselect') {
                    $element->setValues($attribute->getFrontend()->getSelectOptions());
                }
            }

        }

        /* Configurable attributes */
        $usedAttributes = $this->_getOggetto()->getTypeInstance(true)->getUsedOggettoAttributes($this->_getOggetto());
        foreach ($usedAttributes as $attribute) {
            $attributeCode =  $attribute->getAttributeCode();
            $fieldset->addField( 'simple_entity_' . $attributeCode, 'select',  array(
                'label' => $attribute->getFrontend()->getLabel(),
                'name'  => $attributeCode,
                'values' => $attribute->getSource()->getAllOptions(true, true),
                'required' => true,
                'class'    => 'validate-configurable',
                'onchange' => 'superOggetto.showPricing(this, \'' . $attributeCode . '\')'
            ));

            $fieldset->addField('simple_entity_' . $attributeCode . '_pricing_value', 'hidden', array(
                'name' => 'pricing[' . $attributeCode . '][value]'
            ));

            $fieldset->addField('simple_entity_' . $attributeCode . '_pricing_type', 'hidden', array(
                'name' => 'pricing[' . $attributeCode . '][is_percent]'
            ));
        }

        /* Inventory Data */
        $fieldset->addField('simple_entity_inventory_qty', 'text', array(
            'label' => Mage::helper('score')->__('Qty'),
            'name'  => 'stock_data[qty]',
            'class' => 'validate-number',
            'required' => true,
            'value'  => 0
        ));

        $fieldset->addField('simple_entity_inventory_is_in_stock', 'select', array(
            'label' => Mage::helper('score')->__('Stock Availability'),
            'name'  => 'stock_data[is_in_stock]',
            'values' => array(
                array('value'=>1, 'label'=> Mage::helper('score')->__('In Stock')),
                array('value'=>0, 'label'=> Mage::helper('score')->__('Out of Stock'))
            ),
            'value' => 1
        ));

        $stockHiddenFields = array(
            'use_config_min_qty'            => 1,
            'use_config_min_sale_qty'       => 1,
            'use_config_max_sale_qty'       => 1,
            'use_config_backorders'         => 1,
            'use_config_notify_stock_qty'   => 1,
            'is_qty_decimal'                => 0
        );

        foreach ($stockHiddenFields as $fieldName=>$fieldValue) {
            $fieldset->addField('simple_entity_inventory_' . $fieldName, 'hidden', array(
                'name'  => 'stock_data[' . $fieldName .']',
                'value' => $fieldValue
            ));
        }


        $fieldset->addField('create_button', 'note', array(
            'text' => $this->getButtonHtml(
                Mage::helper('score')->__('Quick Create'),
                'superOggetto.quickCreateNewOggetto()',
                'save'
            )
        ));

        $this->setForm($form);
    }

    /**
     * Retrieve currently edited entity object
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    protected function _getOggetto()
    {
        if (!$this->_entity) {
            $this->_entity = Mage::registry('current_entity');
        }
        return $this->_entity;
    }
} // Class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Tab_Super_Config_Simple End
