<?php

class Cafepress_CPCore_Block_Adminhtml_Wms_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('wms_form', array('legend'=>Mage::helper('cpcore')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('cpcore')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('cpcore')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('cpcore')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('cpcore')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('cpcore')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('cpcore')->__('Content'),
          'title'     => Mage::helper('cpcore')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getWmsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getWmsData());
          Mage::getSingleton('adminhtml/session')->setWmsData(null);
      } elseif ( Mage::registry('wms_data') ) {
          $form->setValues(Mage::registry('wms_data')->getData());
      }
      return parent::_prepareForm();
  }
}