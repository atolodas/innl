<?php

class Cafepress_CPWms_Lib_Varien_Data_Form_Element_ProductTypes extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array()){
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml(){
        $html = '';

        $currentCategory = Mage::registry('cp_category_id');

        $product_types = Mage::getModel('merchandise/merchandise')->getCollection()->addFieldToFilter('category_id', $currentCategory);
        if(!file_exists(Mage::getBaseDir('media').'/cafepress')){
            mkdir(Mage::getBaseDir('media').'/cafepress');
        }
        if(!file_exists(Mage::getBaseDir('media').'/cafepress/images')){
            mkdir(Mage::getBaseDir('media').'/cafepress/images');
        }
        $col_max = 4;
        $col = 0;
        $html .= '<table class="cafepress_imagegrid" cellspacing="10"><tr>';
        foreach($product_types as $product_type){
            if($product_type->getCategoryCaption()){
                $image_url = $product_type->getImageUrl();
                $html .= '<td width="150"><div class="cp_type_element" onclick="selectElement(this)">
                    <img width="150" height="150" src="'.$image_url.'"><br/>'.$product_type->getName().'
                    <input class="cp_type_id" type="hidden" value="'.$product_type->getTypeId().'">
                    <input class="cp_type_content" type="hidden" value="'.htmlspecialchars($product_type->getContent()).'">
                    </div></td>';
                $col++;
                if($col >= $col_max){
                } else{
                    $html .= '</tr>';
                    $col = 0;
                }
            }
        }
        $html .= '</table>';
        foreach($product_types as $product_type){
            $html .= $product_type->getImageUrl();
        }
        return $html;
    }
}