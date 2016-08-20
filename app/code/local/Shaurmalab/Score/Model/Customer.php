<?php
class Shaurmalab_Score_Model_Customer extends Mage_Customer_Model_Customer
{
    public function availableForSave() {
        return true;
    }

    /**
     * Prepare customer for delete
     */
    protected function _beforeDelete()
    {
        return Mage_Core_Model_Abstract::_beforeDelete();
    }


    public function getGroup()
    {
        $group_id = $this->getGroupId();
        $collection = Mage::getResourceModel('customer/group_collection')
        ->addFieldToFilter('customer_group_id', $group_id);
        return strtolower(str_replace(' ','',$collection->getFirstItem()->getCode()));
    }

    public function getGroupName()
    {
        $group_id = $this->getGroupId();
        $collection = Mage::getResourceModel('customer/group_collection')
        ->addFieldToFilter('customer_group_id', $group_id);
        return $collection->getFirstItem()->getCode();
    }

    public function getUserpic() { 
       $customerMagemlm  = Mage::getModel('magemlm/customer')->load($this->getEntityId(), 'customer_id');
       if($customerMagemlm->getMagemlmImage()) { 
           $image =  Mage::getBaseUrl('media') . DS . 'magemlm'.DS.$customerMagemlm->getMagemlmImage(); 
       } else { 
         $image = Mage::getDesign()->getSkinUrl('images'.DS.'def_user.jpeg');
       }
       return $image;  
   }


}