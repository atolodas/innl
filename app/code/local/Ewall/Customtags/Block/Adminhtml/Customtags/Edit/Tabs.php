<?php

class Ewall_Customtags_Block_Adminhtml_Customtags_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('customtags_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('customtags')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('customtags')->__('Item Information'),
          'title'     => Mage::helper('customtags')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('customtags/adminhtml_customtags_edit_tab_form')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}
