<?php

class Ewall_Customtags_Block_Adminhtml_Customtags_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('customtags_form', array('legend'=>Mage::helper('customtags')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('customtags')->__('Tag Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));

      $fieldset->addField('filename', 'text', array(
          'label'     => Mage::helper('customtags')->__('Tag Url'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'filename',
	  ));
		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('customtags')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 'Enabled',
                  'label'     => Mage::helper('customtags')->__('Enabled'),
              ),

              array(
                  'value'     => 'Disabled',
                  'label'     => Mage::helper('customtags')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'text', array(
          'name'      => 'content',
          'label'     => Mage::helper('customtags')->__('Popularity'),
          'title'     => Mage::helper('customtags')->__('Popularity'),
         /* 'style'     => 'width:700px; height:500px;',*/
          'wysiwyg'   => false,
          'required'  => true,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getCustomtagsData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getCustomtagsData());
          Mage::getSingleton('adminhtml/session')->setCustomtagsData(null);
      } elseif ( Mage::registry('customtags_data') ) {
          $form->setValues(Mage::registry('customtags_data')->getData());
      }
      return parent::_prepareForm();
  }
}
