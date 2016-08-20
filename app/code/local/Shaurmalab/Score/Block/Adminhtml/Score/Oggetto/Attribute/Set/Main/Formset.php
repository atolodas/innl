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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Attribute_Set_Main_Formset extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Prepares attribute set form
     *
     */
    protected function _prepareForm()
    {
        $data = Mage::getModel('eav/entity_attribute_set')
            ->load($this->getRequest()->getParam('id'));

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('set_name', array('legend'=> Mage::helper('score')->__('Edit Set Name')));
        $fieldset->addField('attribute_set_name', 'text', array(
            'label' => Mage::helper('score')->__('Name'),
            'note' => Mage::helper('score')->__('For internal use.'),
            'name' => 'attribute_set_name',
            'required' => true,
            'class' => 'required-entry validate-no-html-tags',
            'value' => $data->getAttributeSetName()
        ));

        $fieldset->addField('owner', 'text', array(
            'label' => Mage::helper('catalog')->__('Owner'),
            'name' => 'owner',
            'value' => $data->getOwner()
        ));

        $fieldset->addField('is_public', 'select', array(
            'label' => Mage::helper('catalog')->__('Is Public'),
            'name' => 'is_public',
            'values' => array('0'=>'No','1'=>'Yes'),
            'value' => $data->getIsPublic()
        ));

        $field = $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('checkout')->__('Store View'),
                'title'     => Mage::helper('checkout')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value' => $data->getStoreId()
            ));
            $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
            $field->setRenderer($renderer);

        $fieldset->addField('share_to', 'text', array(
            'label' => Mage::helper('catalog')->__('Share To'),
            'name' => 'owner',
            'value' => $data->getShareTo()
        ));

        $fieldset->addField('core_permissions', 'select', array(
            'label' => Mage::helper('catalog')->__('Core permissions'),
            'name' => 'core_permissions',
            'values' => array('0'=>'No','1'=>'Yes'),
            'value' => $data->getCorePermissions()
        ));

        $fieldset->addField('assign_customers', 'select', array(
            'label' => Mage::helper('catalog')->__('Assign Customers'),
            'name' => 'assign_customers',
            'values' => array('0'=>'No','1'=>'Yes'),
            'value' => $data->getAssignCustomers()
        ));


        if( !$this->getRequest()->getParam('id', false) ) {
            $fieldset->addField('gotoEdit', 'hidden', array(
                'name' => 'gotoEdit',
                'value' => '1'
            ));

            $sets = Mage::getModel('eav/entity_attribute_set')
                ->getResourceCollection()
                ->setEntityTypeFilter(Mage::registry('entityType'))
                ->load()
                ->toOptionArray();

            $fieldset->addField('skeleton_set', 'select', array(
                'label' => Mage::helper('score')->__('Based On'),
                'name' => 'skeleton_set',
                'required' => true,
                'class' => 'required-entry',
                'values' => $sets,
            ));
        }

        $form->setMethod('post');
        $form->setUseContainer(true);
        $form->setId('set_prop_form');
        $form->setAction($this->getUrl('*/*/save'));
        $form->setOnsubmit('return false;');
        $this->setForm($form);
    }
}
