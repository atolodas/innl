<?php

class Cafepress_CPCore_Model_Cafepress_Sections extends Mage_Core_Model_Abstract
{
    public function getSectionsList(){
        $list = array();
        if (isset($_SESSION['cp_copy_store']) && $_SESSION['cp_copy_store']!==false){
            $storeId = $_SESSION['cp_copy_store'];
        } else {
            $storeId = Mage::app()->getStore()->getId();
        }

        $appKey = Mage::getStoreConfig('cafepress_common/partner/apikey',$storeId);
        $storeId = Mage::getStoreConfig('cafepress_common/partner/storename',$storeId);
        $url = 'http://open-api.cafepress.com/store.listStoreSections.cp?v=3&appKey='.$appKey.'&storeId='.$storeId;
        $sxml = simplexml_load_file($url);
        for($i = 0; $i < $sxml->storeSection->count(); $i++){
            $section = $sxml->storeSection[$i];
            $list[(string)$section->attributes()->id] = (string)$section->attributes()->caption;
        }
        return $list;
    }

    public function getSectionsListExt($appKey, $storeId){
        $list = array();
        $url = 'http://open-api.cafepress.com/store.listStoreSections.cp?v=3&appKey='.$appKey.'&storeId='.$storeId;
        $sxml = simplexml_load_file($url);
        for($i = 0; $i < $sxml->storeSection->count(); $i++){
            $section = $sxml->storeSection[$i];
//            $list[] = $section->attributes();
            $list[(string)$section->attributes()->id] = array(
                'caption' => (string)$section->attributes()->caption,
                'value' => $sxml->storeSection[$i]->asXML()
            );
        }
        return $list;
    }

    public function getSectionProducts($section_id, $page = 0, $pageSize = 1){
        $products = array();
        if (isset($_SESSION['cp_copy_store']) && $_SESSION['cp_copy_store']!==false){
            $storeId = $_SESSION['cp_copy_store'];
        } else {
            $storeId = Mage::app()->getStore()->getId();
        }
//        $storeId = Mage::app()->getStore()->getId();
        $appKey = Mage::getStoreConfig('cafepress_common/partner/apikey',$storeId);
        $storeId = Mage::getStoreConfig('cafepress_common/partner/storename',$storeId);
        $url = 'http://open-api.cafepress.com/product.listByStoreSection.cp?v=3&appKey='.$appKey.'&storeId='.$storeId.'&sectionId='.$section_id.'&page='.$page.'&pageSize='.$pageSize;
        $sxml = simplexml_load_file($url);
        foreach($sxml as $product_data){
            $data = array(
                'id' => (string)$product_data->attributes()->id,
                'name' => (string)$product_data->attributes()->name,
                'merchandiseId' => (string)$product_data->attributes()->merchandiseId,
                'sellPrice' => (string)$product_data->attributes()->sellPrice,
                'description' => (string)$product_data->attributes()->description,
                'shortDescription' => (string)$product_data->attributes()->shortDescription,
                'categoryCaption' => (string)$product_data->attributes()->categoryCaption,
                'isSellable' => (string)$product_data->attributes()->isSellable,
            );
            $data['colors'] = array();
            for($i = 0; $i < $product_data->color->count(); $i++){
                $data['colors'][] = array(
                    'id' => (string)$product_data->color[$i]->attributes()->id,
                    'name' => (string)$product_data->color[$i]->attributes()->name,
                    'allowed' => (string)$product_data->color[$i]->attributes()->allowed,
                );
            }
            $data['sizes'] = array();
            for($i = 0; $i < $product_data->size->count(); $i++){
                $data['sizes'][] = array(
                    'id' => (string)$product_data->size[$i]->attributes()->id,
                    'name' => (string)$product_data->size[$i]->attributes()->name,
                    'priceDifference' => (string)$product_data->size[$i]->attributes()->priceDifference,
                );
            }
            $data['perspectives'] = array();
            for($i = 0; $i < $product_data->perspective->count(); $i++){
                $data['perspectives'][] = array(
                    'name' => (string)$product_data->perspective[$i]->attributes()->name,
                    'pixelWidth' => (string)$product_data->perspective[$i]->attributes()->pixelWidth,
                    'pixelHeight' => (string)$product_data->perspective[$i]->attributes()->pixelHeight,
                );
            }
            $data['media_configuration'] = array();
            for($i = 0; $i < $product_data->mediaConfiguration->count(); $i++){
                $data['media_configuration'][] = array(
                    'height' => (string)$product_data->mediaConfiguration[$i]->attributes()->height,
                    'width' => (string)$product_data->mediaConfiguration[$i]->attributes()->width,
                    'name' => (string)$product_data->mediaConfiguration[$i]->attributes()->name,
                    'designId' => (string)$product_data->mediaConfiguration[$i]->attributes()->designId,
                );
            }
            $data['product_images'] = array();
            for($i = 0; $i < $product_data->productImage->count(); $i++){
                $data['product_images'][] = array(
                    'colorId' => (string)$product_data->productImage[$i]->attributes()->colorId,
                    'perspectiveName' => (string)$product_data->productImage[$i]->attributes()->perspectiveName,
                    'imageSize' => (string)$product_data->productImage[$i]->attributes()->imageSize,
                    'productUrl' => (string)$product_data->productImage[$i]->attributes()->productUrl,
                );
            }
            $products[] = $data;
        }
        return $products;
    }

    public function getSectionProductsExt($appKey, $storeId, $sectionId){
        $products = array();
        $url = 'http://open-api.cafepress.com/product.listByStoreSection.cp?v=3&appKey='.$appKey.'&storeId='.$storeId.'&sectionId='.$sectionId;
        $sxml = simplexml_load_file($url);
        foreach($sxml as $product_data){
            $products[(string)$product_data->attributes()->id] = array(
                'name' => (string)$product_data->attributes()->name,
                'value' => $product_data->asXML()
            );
        }
        return $products;
    }

    public function getSectionProductsCount($section_id){
        $storeId = Mage::app()->getStore()->getId();
        $appKey = Mage::getStoreConfig('cafepress_common/partner/apikey',$storeId);
        $storeId = Mage::getStoreConfig('cafepress_common/partner/storename',$storeId);
        $url = 'http://open-api.cafepress.com/product.countByStoreSection.cp?v=3&appKey='.$appKey.'&storeId='.$storeId.'&sectionId='.$section_id;
        $sxml = simplexml_load_file($url);
        return (string)$sxml[0];
    }

    public function saveStoreSection($email, $password, $appKey, $value){
        $token = Mage::getModel('cpcore/cafepress_token')->createUserToken($appKey, $email, $password);
        $url = 'http://open-api.cafepress.com/store.saveStoreSection.cp';
        $params = array(
            'appKey' => $appKey,
            'userToken' => $token,
            'value' => $value
        );

        $result = Mage::helper('cpcore')->sendCurlPost($url, $params);
        return $result;
    }

    public function saveSectionProduct($email, $password, $appKey, $value){
        $token = Mage::getModel('cpcore/cafepress_token')->createUserToken($appKey, $email, $password);
        $url = 'http://open-api.cafepress.com/product.save.cp';
        $params = array(
            'appKey' => $appKey,
            'userToken' => $token,
            'value' => $value
        );

        $result = Mage::helper('cpcore')->sendCurlPost($url, $params);
        return $result;
    }
}