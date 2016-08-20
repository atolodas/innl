<?php

class Cafepress_CPCore_Block_Adminhtml_Replacer_Edit_Dynamictable_Newline extends Cafepress_CPCore_Block_Adminhtml_Replacer_Edit_Dynamictable
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cpcore/replacer/edit/dynamictable/newline.phtml');
    }

    public function getReplacer()
    {
        return Mage::registry('current_replacer');
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getLineData(){
        return parent::getLineData();
    }

}
