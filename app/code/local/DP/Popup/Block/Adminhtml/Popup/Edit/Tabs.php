<?php

class DP_Popup_Block_Adminhtml_Popup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('popup_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('popup')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('popup')->__('Item Information'),
          'title'     => Mage::helper('popup')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('popup/adminhtml_popup_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}