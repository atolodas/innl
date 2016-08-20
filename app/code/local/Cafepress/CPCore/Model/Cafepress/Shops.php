<?php

class Cafepress_CPCore_Model_Cafepress_Shops extends Mage_Core_Model_Abstract
{
    public function copy($copy_data){
        $log = '';

        $sections = Mage::getModel('cpcore/cafepress_sections')->getSectionsListExt($copy_data['src_shop_apikey'], $copy_data['src_shop_store']);

        foreach($sections as $section_key => $section){
            $attributes = array(
                'id' => '0',
                'memberId' => $copy_data['dst_shop_partnerid'],
                'storeId' => $copy_data['dst_shop_store'],
            );
            $value = Mage::getModel('cpcore/cafepress')->changeXmlAttributes($section['value'], $attributes);
            $result = Mage::getModel('cpcore/cafepress_sections')
                ->saveStoreSection($copy_data['dst_shop_login'], $copy_data['dst_shop_password'], $copy_data['dst_shop_apikey'], $value);

            $section_info = simplexml_load_string($result);
            $log .= 'Secton '.(string)$section_info[0]->attributes()->caption.' ('.(string)$section_info[0]->attributes()->id.')'.' created'."\r\n";

            $products = Mage::getModel('cpcore/cafepress_sections')
                ->getSectionProductsExt($copy_data['src_shop_apikey'], $copy_data['src_shop_store'], $section_key);

            foreach($products as $product){
                $attributes = array(
//                    'id' => '0',
                    'memberId' => $copy_data['dst_shop_partnerid'],
                    'storeId' => $copy_data['dst_shop_store'],
                    'sectionId' => (string)$section_info[0]->attributes()->id
                );
                $value = Mage::getModel('cpcore/cafepress')->changeXmlAttributes($product['value'], $attributes);
                $result = Mage::getModel('cpcore/cafepress_sections')
                    ->saveSectionProduct($copy_data['dst_shop_login'], $copy_data['dst_shop_password'], $copy_data['dst_shop_apikey'], $value);
                $product_info = simplexml_load_string($result);
                $log .= 'Product '.(string)$product_info[0]->attributes()->name.' ('.(string)$product_info[0]->attributes()->id.')'.' created'."\r\n";
            }
        }

        return $log;
    }

    public function getStoresList($email, $password, $appKey){
        $stores = array();
        $token = Mage::getModel('cpcore/cafepress_token')->createUserToken($appKey, $email, $password);
        $url = 'http://open-api.cafepress.com/store.listStores.cp?appKey='.$appKey.'&userToken='.$token;
        $content = file_get_contents($url);
        $sxml = simplexml_load_string($content);
        for($i = 0; $i < $sxml->store->count(); $i++){
            $id = (string)$sxml->store[$i]->attributes()->id;
            $name = (string)$sxml->store[$i]->attributes()->name;
            $stores[$id] = array(
                'name' => $name,
                'value' => $sxml->store[$i]->asXML()
            );
        }
        return $stores;
    }

    public function saveStore($email, $password, $appKey, $value){
        $token = Mage::getModel('cpcore/cafepress_token')->createUserToken($appKey, $email, $password);
        $url = 'http://open-api.cafepress.com/store.saveStore.cp';
        $params = array(
            'appKey' => $appKey,
            'userToken' => $token,
            'value' => $value
        );

        $result = Mage::helper('cpcore')->sendCurlPost($url, $params);
        return $result;
    }
}