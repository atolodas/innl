<?php

class Shaurmalab_Score_Block_Myoggettos extends Shaurmalab_Score_Block_Oggetto_List
{

 public function getSetId()
	{
        if($this->getSname()) {
            $set = $this->getSname();
        } elseif(isset($_GET['set'])) {
            $set = $_GET['set'];
        } else {
            return false;
        }
          $set = Mage::getResourceModel('eav/entity_attribute_set_collection')
                  ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
            ->addFieldToFilter('attribute_set_name',$set)
            ->getFirstItem(); // TODO: add filter by owner when needed
          return $set->getId();


}

    protected function _getOggettoCollection()
    {
        if(Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();



        $collection = parent::_getOggettoCollection();
        $collection->clear();
        $collection->addAttributeToFilter('owner',$customerId)
            ->addAttributeToSelect('*');
            if($this->getSetId()) {
                $collection->addAttributeToFilter('attribute_set_id',$this->getSetId());
            } else {
                $collection->addStoreFilter(Mage::app()->getStore()->getId());
            }

            if(isset($_GET['qm'])) {
                // TODO: do more relevant search
                $filters = array();
                $attributes = array_keys($collection->getFirstItem()->getData());
                foreach ($attributes as $column) {
                    if(in_array($column,array('name','content','text'))) {
                        $filters[] =  array('attribute'=>$column , 'like'=>'%'.str_replace('-','%',$_GET['qm']).'%');
                    }
                }
                if(count($filters)) {
                    $collection->addAttributeToFilter($filters);
                    //  echo $oggettos-> getSelect();

                }

            }
          //  echo $collection->getSelect();
        $collection->load();

        return  $collection;
        } else {

            Mage::getSingleton('customer/session')->setBeforeAuthUrl('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
            $url = Mage::helper('adminhtml')->getUrl('customer/account/login', array(''));
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect($url)
                ->sendResponse();
        }
    }

}
