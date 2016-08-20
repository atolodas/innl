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
 * meta information tab
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Block_Adminhtml_Events_Edit_Tab_Meta
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return Ultimate_Events_Block_Adminhtml_Events_Edit_Tab_Meta
     * @author Ultimate Module Creator
     */
    protected function _prepareForm(){
        $form = new Varien_Data_Form();
        $form->setFieldNameSuffix('events');
        $this->setForm($form);
        $fieldset = $form->addFieldset('events_meta_form', array('legend'=>Mage::helper('ultimate_events')->__('Meta information')));
        $fieldset->addField('meta_title', 'text', array(
            'label' => Mage::helper('ultimate_events')->__('Meta-title'),
            'name'  => 'meta_title',
        ));
        $fieldset->addField('meta_description', 'textarea', array(
            'name'      => 'meta_description',
            'label'     => Mage::helper('ultimate_events')->__('Meta-description'),
          ));
          $fieldset->addField('meta_keywords', 'textarea', array(
            'name'      => 'meta_keywords',
            'label'     => Mage::helper('ultimate_events')->__('Meta-keywords'),
          ));
          $form->addValues(Mage::registry('current_events')->getData());
        return parent::_prepareForm();
    }
}
