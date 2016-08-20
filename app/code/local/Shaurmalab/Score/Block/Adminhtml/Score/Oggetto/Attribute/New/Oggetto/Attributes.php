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
 * Oggetto attributes tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Attribute_New_Oggetto_Attributes extends Shaurmalab_Score_Block_Adminhtml_Score_Form
{
    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
        /**
         * Initialize entity object as form property
         * for using it in elements generation
         */
        $form->setDataObject(Mage::registry('entity'));

        $fieldset = $form->addFieldset('group_fields', array());

        $attributes = $this->getGroupAttributes();

        $this->_setFieldset($attributes, $fieldset, array('gallery'));

        $values = Mage::registry('entity')->getData();
        /**
         * Set attribute default values for new entity
         */
        if (!Mage::registry('entity')->getId()) {
            foreach ($attributes as $attribute) {
                if (!isset($values[$attribute->getAttributeCode()])) {
                    $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }

        Mage::dispatchEvent('adminhtml_score_oggetto_edit_prepare_form', array('form'=>$form));
        $form->addValues($values);
        $form->setFieldNameSuffix('entity');
        $this->setForm($form);
    }

    protected function _getAdditionalElementTypes()
    {
        $result = array(
            'price'   => Mage::getConfig()->getBlockClassName('score/adminhtml_score_oggetto_helper_form_price'),
            'image'   => Mage::getConfig()->getBlockClassName('score/adminhtml_score_oggetto_helper_form_image'),
            'boolean' => Mage::getConfig()->getBlockClassName('score/adminhtml_score_oggetto_helper_form_boolean')
        );

        $response = new Varien_Object();
        $response->setTypes(array());
        Mage::dispatchEvent('adminhtml_score_oggetto_edit_element_types', array('response'=>$response));

        foreach ($response->getTypes() as $typeName=>$typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }

    protected function _toHtml()
    {
        parent::_toHtml();
        return $this->getForm()->getElement('group_fields')->getChildrenHtml();
    }
}
