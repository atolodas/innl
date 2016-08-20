<?php

class Custom_Funstica_Model_Observer
{
    public function coordsUpdate(Varien_Event_Observer $observer) {
     	$mainOggetto  = $observer->getEvent()->getData('oggetto');
     	$setId = $mainOggetto->getAttributeSetId();
     	
     	if($setId == 45 || $setId == 48) {  // Offer or Extra ofer
     		if($mainOggetto->getLat() && $mainOggetto->getLng()) { 
     			$childs = $mainOggetto->getRelatedOggettoCollection()
				            ->addAttributeToFilter('visibility',array('neq'=>1))
				            
				            ->addAttributeToFilter('attribute_set_id', 33) // Ideas
							->addAttributeToSelect('*')

				            ->addStoreFilter()
				        ;	

			
				foreach ($childs as $child) {
					//$child = Mage::getModel('score/oggetto')->load($child->getId());
					$parents = $child->getParentOggettoCollection()
							    ->addAttributeToFilter('visibility',array('neq'=>1))
							    ->addAttributeToFilter('attribute_set_id', array(45, 48)) // Offers
								->addAttributeToSelect('*')
							    ->addStoreFilter();
					$lat = array(); $lng = array();
					$pairs = array();
					$prices = array();
					foreach ($parents as $parent) {
						if($parent->getFprice()) $prices[] = $parent->getFprice();

						if($parent->getLat() && $parent->getLng()) { 
							$pairs[] = $parent->getLat().'-'.$parent->getLng();
						}
					}

					if(empty($prices)) $minPrice = "0"; 
					else $minPrice = min($prices);

					$unique = array_unique($pairs);

					foreach ($unique as $key => $value) {
						list($lt, $lg) = explode('-', $value);
						$lat[] = trim($lt);
						$lng[] = trim($lg);
					}
					echo $minPrice."\n";
					$child->setFprice($minPrice)->setLat(implode(',', $lat))->setLng(implode(',', $lng))->save();
				}

     		}
     	}
    }
}