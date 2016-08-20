<?php

class Cafepress_CPWms_SyncController extends Mage_Core_Controller_Front_Action
{
    public function getAttributeValues($attribute) {
        $result = array();
        $attribute = Mage::getModel('eav/config')
                ->getAttribute('catalog_product', $attribute)->setStoreId(0);
        $options = $attribute->getFrontend()->getSelectOptions();
        foreach ($options as $option) {
            if(strlen($option['label']) > 0) $result[$option['value']] = $option['label'];
        }
        return $result;
    }

    public function indexAction() {
        $storeId = 0;
        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                ->setStoreId($storeId)
                ->loadByAttribute('name','get_colors');

        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                ->setStoreId($storeId)
                ->load($xmlformatModel->getId());

        if (!$xmlformatModel->getName()) {
            die('No format With name "'.'get_colors'.'"!');
        }

        $xmlformatModel->processRequest();

        $result = $xmlformatModel->processResponse();
        try {
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');
         
            $colors = $this->getAttributeValues('color');
            for($i = 0; $i < count($result['colorNum']); $i++) {
                if(in_array($result['colorNum'][$i].'-'.$result['colorDesc'][$i], $colors)) {
                    $write->query("UPDATE eav_attribute_option SET custom_id = '{$result['colorNum'][$i]}' WHERE option_id = (SELECT option_id FROM eav_attribute_option_value WHERE value = '{$result['colorNum'][$i]}-{$result['colorDesc'][$i]}' LIMIT 1)");
                    echo 'update: '.$result['colorNum'][$i].'-'.$result['colorDesc'][$i].'<br/>';
                }
                else {
                    $attribute_id = Mage::getModel('eav/config')->getAttribute('catalog_product', 'color')->getId();
                    $new_option = Mage::getModel('eav/entity_attribute_option')->setAttributeId($attribute_id)->setStoreId($storeId)->save();
                    $option = array();
                    $option['attribute_id'] = $attribute_id;
                    $option['value'][$new_option->getId()][$storeId] = $result['colorNum'][$i].'-'.$result['colorDesc'][$i];
                    $option['value'][$new_option->getId()][1] = $result['colorDesc'][$i];
                    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                    $setup->addAttributeOption($option);
                    $write->query("UPDATE eav_attribute_option SET custom_id = '{$result['colorNum'][$i]}' WHERE option_id = (SELECT option_id FROM eav_attribute_option_value WHERE value = '{$result['colorNum'][$i]}-{$result['colorDesc'][$i]}' LIMIT 1)");
                    echo 'insert: '.$result['colorNum'][$i].'-'.$result['colorDesc'][$i].'<br/>';
                }
            }
            echo 'Colors synchronization completed.<br/><br/>';
        } catch (Exception $e) {
            echo 'Error: '.$e->getMessage().'<br/>';
        }

        $storeId = 0;
        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                ->setStoreId($storeId)
                ->loadByAttribute('name','get_sizes');

        $xmlformatModel = Mage::getModel('cpwms/xmlformat_format_transformer')
                ->setStoreId($storeId)
                ->load($xmlformatModel->getId());

        if (!$xmlformatModel->getName()) {
            die('No format With name "'.'get_sizes'.'"!');
        }

        $xmlformatModel->processRequest();

        $result = $xmlformatModel->processResponse();
        try {
            $write = Mage::getSingleton('core/resource')->getConnection('core_write');

            $sizes = $this->getAttributeValues('size');
            for($i = 0; $i < count($result['sizeNum']); $i++) {
                if(in_array($result['sizeNum'][$i].'-'.$result['sizeDesc'][$i], $sizes)) {
                    $write->query("UPDATE eav_attribute_option SET custom_id = '{$result['sizeNum'][$i]}' WHERE option_id = (SELECT option_id FROM eav_attribute_option_value WHERE value = '{$result['sizeNum'][$i]}-{$result['sizeDesc'][$i]}' LIMIT 1)");
                    echo 'update: '.$result['sizeNum'][$i].'-'.$result['sizeDesc'][$i].'<br/>';
                }
                else {
                    $attribute_id = Mage::getModel('eav/config')->getAttribute('catalog_product', 'size')->getId();
                    $new_option = Mage::getModel('eav/entity_attribute_option')->setAttributeId($attribute_id)->setStoreId($storeId)->save();
                    $option = array();
                    $option['attribute_id'] = $attribute_id;
                    $option['value'][$new_option->getId()][$storeId] = $result['sizeNum'][$i].'-'.$result['sizeDesc'][$i];
                    $option['value'][$new_option->getId()][1] = $result['sizeDesc'][$i];
                    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                    $setup->addAttributeOption($option);
                    $write->query("UPDATE eav_attribute_option SET custom_id = '{$result['sizeNum'][$i]}' WHERE option_id = (SELECT option_id FROM eav_attribute_option_value WHERE value = '{$result['sizeNum'][$i]}-{$result['sizeDesc'][$i]}' LIMIT 1)");
                    echo 'insert: '.$result['sizeNum'][$i].'-'.$result['sizeDesc'][$i].'<br/>';
                }
            }
            echo 'Sizes synchronization completed.<br/>';
        } catch (Exception $e) {
            echo 'Error: '.$e->getMessage().'<br/>';
        }
    }

    public function productTypesAction(){

        $merchandise = Mage::getModel('merchandise/merchandise')->getCollection();
        $existing = array();
        foreach($merchandise as $type){
            $existing[] = $type->getTypeId();
        }
        $merchandise_collection = Mage::getModel('cpwms/cafepress_merchandise')->getMerchandiseCollectionForSync();
        foreach ($merchandise_collection as $productType){
            $sxml = simplexml_load_string($productType['all_block_content']);
            if(!array_key_exists($productType['id'], $existing)){
                $merchandise = Mage::getModel('merchandise/merchandise');
            } else{
                $merchandise = Mage::getModel('merchandise/merchandise')->load($existing[$productType['id']]);
            }
            $merchandise->setTypeId($productType['id']);
            $merchandise->setName($productType['name']);
            $merchandise->setContent($productType['all_block_content']);
            $merchandise->setCategoryId((string)$sxml[0]->attributes()->categoryId);
            $merchandise->setCategoryCaption((string)$sxml[0]->attributes()->categoryCaption);
            $image_url = (string)$sxml[0]->attributes()->defaultBlankProductUrl;
            $merchandise->setImageUrl(Mage::getBaseUrl('media').'cafepress/images/'.basename($image_url));
            $merchandise->save();

            $image_path = Mage::getBaseDir().'/media/cafepress/images/'.basename($image_url);
            if(!file_exists($image_path)){
                @copy($image_url, $image_path);
            }
        }

        echo "Success";
    }
}

?>
