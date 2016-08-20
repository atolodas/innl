<?php

class Snowcommerce_Seo_Block_Adminhtml_Seo_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
  	  $model = $this;
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('seo_form', array('legend'=>Mage::helper('seo')->__('Meta information')));


      $fieldset->addField('meta_title', 'text', array(
          'label'     => Mage::helper('seo')->__('Meta Title'),
          'name'      => 'meta_title',
      ));


      $fieldset->addField('meta_keyword', 'editor', array(
          'label'     => Mage::helper('seo')->__('Meta Keyword'),
          'name'      => 'meta_keyword',
      ));
      
      $fieldset->addField('meta_description', 'editor', array(
          'label'     => Mage::helper('seo')->__('Meta Description'),
          'name'      => 'meta_description',
      ));

      if ( Mage::getSingleton('adminhtml/session')->getSeoData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getSeoData());
          Mage::getSingleton('adminhtml/session')->setSeoData(null);
      } elseif ( Mage::registry('seo_data') ) {
          $form->setValues(Mage::registry('seo_data')->getData());
      }
      return parent::_prepareForm();
  }
}