<?php
class Cafepress_CPWms_Helper_Replacer extends Mage_Core_Helper_Abstract
{
    protected $types = array(
        array(
            'id'    => 1,
            'name'  => 'constant',
            'label' => 'Const'
        ),
        array(
            'id'    => 2,
            'name'  => 'match',
            'label' => 'Match'
        ),
        array(
            'id'    => 0,
            'name'  => 'disable',
            'label' => 'Disabled'
        ),
    );


    public function getWebsiteNameById($websiteId){
        return Mage::getSingleton('adminhtml/system_store')->getWebsiteName($websiteId);
    }

    public function getStores(){
        $result = array();
        $stores = Mage::app()->getStores();

        foreach($stores as $store){
            $store->setWebsiteName($this->getWebsiteNameById($store->getWebsiteId()));
            $result[] = $store->getData();
        }
        return $result;
    }

    public function replace($construction,$replacedValue,$storeId){
        return Mage::getSingleton('cpwms/replacer')->replaceValue($construction,$replacedValue,$storeId);
    }
    
    public function getLineTypes(){
        return $this->types;
    }

}
