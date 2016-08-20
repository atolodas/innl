<?php

class Oggetto_Cacherecords_Block_Adminhtml_Cacherecords_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('cacherecords_form', array('legend'=>Mage::helper('cacherecords')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('cacherecords')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'file', array(
          'label'     => Mage::helper('cacherecords')->__('File'),
          'required'  => false,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('cacherecords')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('cacherecords')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('cacherecords')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('cacherecords')->__('Content'),
          'title'     => Mage::helper('cacherecords')->__('Content'),
          'style'     => 'width:700px; height:500px;',
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCacherecordsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCacherecordsData());
          Mage::getSingleton('adminhtml/session')->setCacherecordsData(null);
      } elseif ( Mage::registry('cacherecords_data') ) {
          $form->setValues(Mage::registry('cacherecords_data')->getData());
      }
      return parent::_prepareForm();
  }
}