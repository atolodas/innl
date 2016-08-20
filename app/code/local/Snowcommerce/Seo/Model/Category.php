<?php
class Snowcommerce_Seo_Model_Category extends Mage_Core_Model_Abstract
{
    /**
     * Init resource model
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('pblocks/pblocks');
    }



    public function toOptionArray(){
        $collection = Mage::getModel('catalog/category')->getCollection()->addAttributeToFilter('is_active',1)->addAttributeToSelect('path')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('level')
            ->addOrder('name','asc');
        $option_array = array();
        foreach($collection as $webform) {


            $option_array[]= array('value'=>$webform->getId(), 'label' => $webform->getName());
        }
        return $option_array;
    }
}
