<?php 
class Custom_Funstica_Block_Newsfeed extends Shaurmalab_Score_Block_Oggetto_All
{
	public function getOggettos()
    {
		$customer = Mage::getModel('customer/customer')->load(Mage::getSingleton('customer/session')->getCustomer()->getId());
		//if($customer->getCityId()) { 
			$objects = Mage::getModel('score/oggetto')->getCollection()
						->addAttributeToFilter('attribute_set_id',array('in'=>array(43))) ///,38,33,36,42)))
						->addStoreFilter()
						->addAttributeToSelect('*');
						$objects->addAttributeToSelect('city_dict','left');
			$objects->addAttributeToFilter(Mage::helper('score')->getLikeOrEmptyArray('city_dict',$customer->getCityId()));
				$objects->addAttributeToSort('created_at', 'desc');
		// } else { 
		// 	return false;
		// }

		return $objects;

	}


	protected function _getOggettoCollection()
    {

        if (is_null($this->_oggettoCollection)) {
            $layer = $this->getLayer();

            $this->_oggettoCollection = $this->getOggettos();

            if(is_object($layer)) $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        }

        return $this->_oggettoCollection;
    }

    public function getKind() { 
    	return 'newsfeed';
    }
}
