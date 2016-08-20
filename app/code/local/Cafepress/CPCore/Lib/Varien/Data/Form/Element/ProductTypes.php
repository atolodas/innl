<?php

class Cafepress_CPCore_Lib_Varien_Data_Form_Element_ProductTypes extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array()){
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml(){
        $html = '';

        $product_types = Mage::getModel('merchandise/merchandise')->getCollection();
        if(!file_exists(Mage::getBaseDir('media').'/cafepress')){
            mkdir(Mage::getBaseDir('media').'/cafepress');
        }
        if(!file_exists(Mage::getBaseDir('media').'/cafepress/images')){
            mkdir(Mage::getBaseDir('media').'/cafepress/images');
        }
        $col_max = 3;
        $col = 0;
        $html .= '<table class="cafepress_imagegrid" cellspacing="10"><tr>';
        $index = 0;
        foreach($product_types as $product_type){
            $sxml = simplexml_load_string($product_type['content']);
            $image_url = (string)$sxml[0]->attributes()->defaultBlankProductUrl;
            $image_url = Mage::getBaseUrl('media').'/cafepress/images/'.basename($image_url);
            $html .= '<td width="150"><div class="cp_type_element" onclick="selectElement(this)">
                <img width="150" height="150" src="'.$image_url.'"><br/>'.$product_type['name'].'
                <input class="cp_type_id" type="hidden" value="'.$product_type['type_id'].'">
                <input class="cp_type_content" type="hidden" value="'.htmlspecialchars($product_type['content']).'">
                </div></td>';
            if($col < $col_max){
                $col++;
            } else{
                $html .= '</tr>';
                $col = 0;
            }
//            $index++;
//            if($index == 12){
//                break;
//            }
        }
        $html .= '</table>';
        return $html;
    }
}