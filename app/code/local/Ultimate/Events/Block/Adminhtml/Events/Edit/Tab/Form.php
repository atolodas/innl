<?php
/**
 * Ultimate_Events extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Ultimate
 * @package        Ultimate_Events
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Events edit form tab
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Block_Adminhtml_Events_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return Events_Events_Block_Adminhtml_Events_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('events_');
        $form->setFieldNameSuffix('events');
        $this->setForm($form);
        $fieldset = $form->addFieldset('events_form', array('legend'=>Mage::helper('ultimate_events')->__('Events')));

        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('ultimate_events')->__('Event title'),
            'name'  => 'title',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('description', 'textarea', array(
            'label' => Mage::helper('ultimate_events')->__('Event description'),
            'name'  => 'description',
            'required'  => true,
            'class' => 'required-entry',

        ));

        $fieldset->addField('date', 'date', array(
            'label' => Mage::helper('ultimate_events')->__('Event date'),
            'name'  => 'date',
            'required'  => true,
            'class' => 'required-entry',

            'image' => $this->getSkinUrl('images/grid-cal.gif'),
            'format'  => Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
        ));
        $fieldset->addField('url_key', 'text', array(
            'label' => Mage::helper('ultimate_events')->__('Url key'),
            'name'  => 'url_key',
            'note'    => Mage::helper('ultimate_events')->__('Relative to Website Base URL')
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('ultimate_events')->__('Status'),
            'name'  => 'status',
            'values'=> array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('ultimate_events')->__('Enabled'),
                ),
                array(
                    'value' => 0,
                    'label' => Mage::helper('ultimate_events')->__('Disabled'),
                ),
            ),
        ));
//        $fieldset->addField('in_rss', 'select', array(
//            'label' => Mage::helper('ultimate_events')->__('Show in rss'),
//            'name'  => 'in_rss',
//            'values'=> array(
//                array(
//                    'value' => 1,
//                    'label' => Mage::helper('ultimate_events')->__('Yes'),
//                ),
//                array(
//                    'value' => 0,
//                    'label' => Mage::helper('ultimate_events')->__('No'),
//                ),
//            ),
//        ));
        if (Mage::app()->isSingleStoreMode()){
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('current_events')->setStoreId(Mage::app()->getStore(true)->getId());
        }
//        $fieldset->addField('allow_comment', 'select', array(
//            'label' => Mage::helper('ultimate_events')->__('Allow Comments'),
//            'name'  => 'allow_comment',
//            'values'=> Mage::getModel('ultimate_events/adminhtml_source_yesnodefault')->toOptionArray()
//        ));
        $formValues = Mage::registry('current_events')->getDefaultValues();
        if (!is_array($formValues)){
            $formValues = array();
        }
        if (Mage::getSingleton('adminhtml/session')->getEventsData()){
            $formValues = array_merge($formValues, Mage::getSingleton('adminhtml/session')->getEventsData());
            Mage::getSingleton('adminhtml/session')->setEventsData(null);
        }
        elseif (Mage::registry('current_events')){
            $formValues = array_merge($formValues, Mage::registry('current_events')->getData());
        }
        $form->setValues($formValues);
        return parent::_prepareForm();
    }
}
