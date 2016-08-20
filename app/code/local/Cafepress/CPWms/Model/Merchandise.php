<?php

class Cafepress_CPWms_Model_Merchandise extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('merchandise/merchandise');
    }

    public function getContentArray(){
        $result = array();
        $merchandise_collection = $this->getCollection();
        foreach($merchandise_collection as $merchandise){
            $result[$merchandise['type_id']] = $merchandise['content'];
        }
        return $result;
    }

    public function getFormattedData(){
        $result = array();
        foreach($this->getCollection() as $merchandise){
            $sxml = simplexml_load_string($merchandise->getContent());
            $result[] = array(
            'id' => $merchandise->getTypeId(),
            'all_block_content' => $merchandise->getContent(),
            'name' => $merchandise->getName(),
            'sellPrice' => (string)$sxml[0]->attributes()->sellPrice,
            'description' => (string)$sxml[0]->description,
            'categoryId' => (string)$sxml[0]->attributes()->categoryId,
            'categoryCaption' => (string)$sxml[0]->attributes()->categoryCaption,
            'image_url' => (string)$sxml[0]->attributes()->defaultBlankProductUrl);
        }
        return $result;
    }

    public function getCategories(){
        $categories = array();
        $product_types = $this->getFormattedData();
        foreach($product_types as $product_type){
            if($product_type['categoryCaption']){
                $categories[$product_type['categoryId']] = $product_type['categoryCaption'];
            }
        }
        return $categories;
    }

    public function getCategoryProducts($category_id){
        $result = array();
        foreach($this->getCollection() as $merchandise){
            $sxml = simplexml_load_string($merchandise->getContent());
            if((string)$sxml[0]->attributes()->categoryCaption){
                if((string)$sxml[0]->attributes()->categoryId == $category_id){
                    $result[] = array(
                        'type_id' => $merchandise->getTypeId(),
                        'all_block_content' => $merchandise->getContent(),
                        'name' => $merchandise->getName(),
                        'image_url' => (string)$sxml[0]->attributes()->defaultBlankProductUrl);
                }
            }
        }
        return $result;
    }
}
