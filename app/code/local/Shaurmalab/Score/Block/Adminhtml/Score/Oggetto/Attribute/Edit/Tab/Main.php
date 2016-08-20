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
 * Oggetto attribute add/edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Attribute_Edit_Tab_Main extends Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
{
    /**
     * Adding entity form elements for editing attribute
     *
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Attribute_Edit_Tab_Main
     */
    protected function _prepareForm()
    {
        parent::_prepareForm();
        $attributeObject = $this->getAttributeObject();
        /* @var $form Varien_Data_Form */
        $form = $this->getForm();
        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $frontendInputElm = $form->getElement('frontend_input');
        $additionalTypes = array(
            array(
                'value' => 'price',
                'label' => Mage::helper('score')->__('Price')
            ),
            array(
                'value' => 'media_image',
                'label' => Mage::helper('score')->__('Media Image')
            )
        );
        if ($attributeObject->getFrontendInput() == 'gallery') {
            $additionalTypes[] = array(
                'value' => 'gallery',
                'label' => Mage::helper('score')->__('Gallery')
            );
        }

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_entity_attribute_types', array('response'=>$response));
        $_disabledTypes = array();
        $_hiddenFields = array();
        foreach ($response->getTypes() as $type) {
            $additionalTypes[] = $type;
            if (isset($type['hide_fields'])) {
                $_hiddenFields[$type['value']] = $type['hide_fields'];
            }
            if (isset($type['disabled_types'])) {
                $_disabledTypes[$type['value']] = $type['disabled_types'];
            }
        }
        Mage::register('attribute_type_hidden_fields', $_hiddenFields);
        Mage::register('attribute_type_disabled_types', $_disabledTypes);

        $frontendInputValues = array_merge($frontendInputElm->getValues(), $additionalTypes);
        $frontendInputElm->setValues($frontendInputValues);

        $yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $scopes = array(
            Shaurmalab_Score_Model_Resource_Eav_Attribute::SCOPE_STORE =>Mage::helper('score')->__('Store View'),
            Shaurmalab_Score_Model_Resource_Eav_Attribute::SCOPE_WEBSITE =>Mage::helper('score')->__('Website'),
            Shaurmalab_Score_Model_Resource_Eav_Attribute::SCOPE_GLOBAL =>Mage::helper('score')->__('Global'),
        );

        if ($attributeObject->getAttributeCode() == 'status' || $attributeObject->getAttributeCode() == 'tax_class_id') {
            unset($scopes[Shaurmalab_Score_Model_Resource_Eav_Attribute::SCOPE_STORE]);
        }

        $fieldset->addField('is_global', 'select', array(
            'name'  => 'is_global',
            'label' => Mage::helper('score')->__('Scope'),
            'title' => Mage::helper('score')->__('Scope'),
            'note'  => Mage::helper('score')->__('Declare attribute value saving scope'),
            'values'=> $scopes
        ), 'attribute_code');

        $fieldset->addField('is_public', 'select', array(
            'name'  => 'is_public',
            'label' => Mage::helper('score')->__('Is available for public'),
            'title' => Mage::helper('score')->__('Is available for public'),
            'values'=> array(0=>'No',1=>'Yes'),
        ), 'attribute_code');

        $fieldset->addField('is_for_logged_in', 'select', array(
            'name'  => 'is_for_logged_in',
            'label' => Mage::helper('score')->__('Is available for Logged in visitors only'),
            'title' => Mage::helper('score')->__('Is available for Logged in visitors only'),
            'values'=> array(0=>'No',1=>'Yes'),
        ), 'attribute_code');

        $fieldset->addField('is_for_edit', 'select', array(
            'name'  => 'is_for_edit',
            'label' => Mage::helper('score')->__('Is available on Edit form'),
            'title' => Mage::helper('score')->__('Is available on Edit form'),
            'values'=> array(   '1' => Mage::helper('score')->__('Yes'),
                '0' => Mage::helper('score')->__('Hidden'),
                '2' => Mage::helper('score')->__('Label'),
            '3' => Mage::helper('score')->__('Under button')),
        ), 'attribute_code');

        $fieldset->addField('apply_to', 'apply', array(
            'name'        => 'apply_to[]',
            'label'       => Mage::helper('score')->__('Apply To'),
            'values'      => Shaurmalab_Score_Model_Oggetto_Type::getOptions(),
            'mode_labels' => array(
                'all'     => Mage::helper('score')->__('All Oggetto Types'),
                'custom'  => Mage::helper('score')->__('Selected Oggetto Types')
            ),
            'required'    => true
        ), 'frontend_class');

        $fieldset->addField('is_configurable', 'select', array(
            'name' => 'is_configurable',
            'label' => Mage::helper('score')->__('Use To Create Configurable Oggetto'),
            'values' => $yesnoSource,
        ), 'apply_to');

        // frontend properties fieldset
        $fieldset = $form->addFieldset('front_fieldset', array('legend'=>Mage::helper('score')->__('Frontend Properties')));

        $fieldset->addField('is_searchable', 'select', array(
            'name'     => 'is_searchable',
            'label'    => Mage::helper('score')->__('Use in Quick Search'),
            'title'    => Mage::helper('score')->__('Use in Quick Search'),
            'values'   => $yesnoSource,
        ));

        $fieldset->addField('is_visible_in_advanced_search', 'select', array(
            'name' => 'is_visible_in_advanced_search',
            'label' => Mage::helper('score')->__('Use in Advanced Search'),
            'title' => Mage::helper('score')->__('Use in Advanced Search'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => Mage::helper('score')->__('Comparable on Front-end'),
            'title' => Mage::helper('score')->__('Comparable on Front-end'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => Mage::helper('score')->__("Use In Layered Navigation"),
            'title' => Mage::helper('score')->__('Can be used only with score input type Dropdown, Multiple Select and Price'),
            'note' => Mage::helper('score')->__('Can be used only with score input type Dropdown, Multiple Select and Price'),
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('score')->__('No')),
                array('value' => '1', 'label' => Mage::helper('score')->__('Filterable (with results)')),
                array('value' => '2', 'label' => Mage::helper('score')->__('Filterable (no results)')),
            ),
        ));

        $fieldset->addField('is_filterable_in_search', 'select', array(
            'name' => 'is_filterable_in_search',
            'label' => Mage::helper('score')->__("Use In Search Results Layered Navigation"),
            'title' => Mage::helper('score')->__('Can be used only with score input type Dropdown, Multiple Select and Price'),
            'note' => Mage::helper('score')->__('Can be used only with score input type Dropdown, Multiple Select and Price'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_used_for_promo_rules', 'select', array(
            'name' => 'is_used_for_promo_rules',
            'label' => Mage::helper('score')->__('Use for Promo Rule Conditions'),
            'title' => Mage::helper('score')->__('Use for Promo Rule Conditions'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('position', 'text', array(
            'name' => 'position',
            'label' => Mage::helper('score')->__('Position'),
            'title' => Mage::helper('score')->__('Position in Layered Navigation'),
            'note' => Mage::helper('score')->__('Position of attribute in layered navigation block'),
            'class' => 'validate-digits',
        ));

        $fieldset->addField('is_wysiwyg_enabled', 'select', array(
            'name' => 'is_wysiwyg_enabled',
            'label' => Mage::helper('score')->__('Enable WYSIWYG'),
            'title' => Mage::helper('score')->__('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ));

        $htmlAllowed = $fieldset->addField('is_html_allowed_on_front', 'select', array(
            'name' => 'is_html_allowed_on_front',
            'label' => Mage::helper('score')->__('Allow HTML Tags on Frontend'),
            'title' => Mage::helper('score')->__('Allow HTML Tags on Frontend'),
            'values' => $yesnoSource,
        ));
        if (!$attributeObject->getId() || $attributeObject->getIsWysiwygEnabled()) {
            $attributeObject->setIsHtmlAllowedOnFront(1);
        }

        $fieldset->addField('is_visible_on_front', 'select', array(
            'name'      => 'is_visible_on_front',
            'label'     => Mage::helper('score')->__('Visible on Oggetto View Page on Front-end'),
            'title'     => Mage::helper('score')->__('Visible on Oggetto View Page on Front-end'),
            'values'    => $yesnoSource,
        ));

        $fieldset->addField('used_in_oggetto_listing', 'select', array(
            'name'      => 'used_in_oggetto_listing',
            'label'     => Mage::helper('score')->__('Used in Oggetto Listing'),
            'title'     => Mage::helper('score')->__('Used in Oggetto Listing'),
            'note'      => Mage::helper('score')->__('Depends on design theme'),
            'values'    => $yesnoSource,
        ));
        $fieldset->addField('used_for_sort_by', 'select', array(
            'name'      => 'used_for_sort_by',
            'label'     => Mage::helper('score')->__('Used for Sorting in Oggetto Listing'),
            'title'     => Mage::helper('score')->__('Used for Sorting in Oggetto Listing'),
            'note'      => Mage::helper('score')->__('Depends on design theme'),
            'values'    => $yesnoSource,
        ));

        $form->getElement('apply_to')->setSize(5);

        if ($applyTo = $attributeObject->getApplyTo()) {
            $applyTo = is_array($applyTo) ? $applyTo : explode(',', $applyTo);
            $form->getElement('apply_to')->setValue($applyTo);
        } else {
            $form->getElement('apply_to')->addClass('no-display ignore-validate');
        }

        // define field dependencies
        $this->setChild('form_after', $this->getLayout()->createBlock('adminhtml/widget_form_element_dependence')
            ->addFieldMap("is_wysiwyg_enabled", 'wysiwyg_enabled')
            ->addFieldMap("is_html_allowed_on_front", 'html_allowed_on_front')
            ->addFieldMap("frontend_input", 'frontend_input_type')
            ->addFieldDependence('wysiwyg_enabled', 'frontend_input_type', 'textarea')
            ->addFieldDependence('html_allowed_on_front', 'wysiwyg_enabled', '0')
        );

        Mage::dispatchEvent('adminhtml_score_oggetto_attribute_edit_prepare_form', array(
            'form'      => $form,
            'attribute' => $attributeObject
        ));

        return $this;
    }

    /**
     * Retrieve additional element types for entity attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'apply'         => Mage::getConfig()->getBlockClassName('score/adminhtml_score_oggetto_helper_form_apply'),
        );
    }
}
