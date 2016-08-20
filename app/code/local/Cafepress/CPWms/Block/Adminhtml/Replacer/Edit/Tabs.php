<?php

class Cafepress_CPWms_Block_Adminhtml_Replacer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('replacer_info_tabs');
        $this->setDestElementId('replacer_edit_form');
        $this->setTitle(Mage::helper('cpwms')->__('WMS Replacer Information'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('general', array(
            'label'     => Mage::helper('cpwms')->__('General'),
            'content'   => $this->_translateHtml($this->getLayout()->createBlock('cpwms/adminhtml_replacer_edit_tabs_form_general')->toHtml()),
            'active'    => true
        ));

        return parent::_prepareLayout();
    }

    protected function _translateHtml($html)
    {
        Mage::getSingleton('core/translate_inline')->processResponseBody($html);
        return $html;
    }
}
