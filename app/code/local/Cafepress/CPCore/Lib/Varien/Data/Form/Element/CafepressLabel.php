<?php

class Cafepress_CPCore_Lib_Varien_Data_Form_Element_CafepressLabel extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array()){
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml(){
        $html = '';
        $product = Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('id'));
        if($product->getCpImage() != 'no_selection'){
            $html .= '<img width=150 height=150 src=\''.Mage::getBaseUrl('media').'catalog/product/'.$product->getCpImage().'\'><br/><br/>';
            $html .= '<input type="hidden" name="oldCpFile" id="oldCpFile" value="'.Mage::getBaseDir('media').'/catalog/product'.$product->getCpImage().'">';
        }
        return $html;
    }
}