<?php

class Cafepress_CPCore_Block_Adminhtml_Synchronize extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    protected function _construct()
    {
        parent::_construct();
    }

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $html ='';
        $html .= '<script type="text/javascript">
            function cpsync(){
                var url = "'.Mage::getUrl('cpcore/sync/productTypes',array('_secure'=>true)).'";
                new Ajax.Request(url, {
                    method: "post",
                });
                }
        </script>';
        $html .= '<button type="button" onclick="cpsync()"><span>Start</span></button>';
        return $html;
    }
}