<?php

class Snowcommerce_Seo_Block_Adminhtml_Seo_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('seo_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('seo')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
  	
  	
      $this->addTab('form_section', array(
          'label'     => Mage::helper('seo')->__('Page Information'),
          'title'     => Mage::helper('seo')->__('Page Information'),
          'content'   => $this->getLayout()->createBlock('seo/adminhtml_seo_edit_tab_form')->toHtml(),
      ));
      
      $this->addTab('meta_section', array(
          'label'     => Mage::helper('seo')->__('Meta Information'),
          'title'     => Mage::helper('seo')->__('Meta Information'),
          'content'   => $this->getLayout()->createBlock('seo/adminhtml_seo_edit_tab_meta')->toHtml(),
      ));
      
      if(Mage::registry('seo_data') && Mage::registry('seo_data')->getId()) {
	      /**
	      * Don't display website tab for single mode
	      */
	            if (!Mage::app()->isSingleStoreMode()) {
	                $this->addTab('websites', array(
	                    'label'     => Mage::helper('catalog')->__('Websites'),
	                    'content'   => $this->getLayout()->createBlock('seo/adminhtml_seo_edit_tab_websites')->toHtml(),
	                ));
	            }
      }
      return parent::_beforeToHtml();
  }
}