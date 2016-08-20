<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Score oggetto related items block
 *
 * @category   Mage
 * @package    Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Custom_Funstica_Block_Related extends Shaurmalab_Score_Block_Oggetto_List_Related 
{
    /**
     * Default MAP renderer type
     *
     * @var string
     */
    protected $_mapRenderer = 'msrp_noform';

    protected $_itemCollection;

	protected $_parentCollection; 
	
    protected function _prepareData()
    {
        $oggetto = Mage::registry('current_oggetto');
        if(!is_object($oggetto)) $oggetto = Mage::registry('oggetto');
        
        /* @var $oggetto Shaurmalab_Score_Model_Oggetto */
        $filters = $this->getPrefilter();
        $filters = explode(',',$filters);
        foreach($filters as $k=>$filter) {

              
                if($filter) {
                    if(substr_count($filter, '<=')) { 

                        list($code,$value) = explode('<=',$filter);
                          $value = $value*1;
                    } elseif(substr_count($filter, '>=')) { 
                        list($code,$value) = explode('>=',$filter);
                          $value = $value*1;
                    } else { 
                            list($code,$value) = explode('=',$filter);
                    }

                    $filtersData[$code] = $value;
                    $newFilters[$code] = $filter;

            }
        }

        if(!$this->_itemCollection) { 
        $this->_itemCollection = $oggetto->getRelatedOggettoCollection()
            ->addAttributeToFilter('visibility',array('neq'=>1))
            ->addAttributeToSelect('*')
            ->addStoreFilter()
        ;



        if($this->getSet()) { 
            $this->_itemCollection->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode($this->getSet()));
        }

        if(isset($newFilters) && is_array($newFilters)) { 
                foreach($newFilters as $filter) {


                    if($filter) {
                        if(substr_count($filter, '<=')) { 
                                list($code,$value) = explode('<=',$filter);
                                 $this->_itemCollection->addAttributeToSelect($code, 'left');
                                if(substr_count($code, '_date')==0) $value = $value*1;
                                if($code && $value) {  $this->_itemCollection->addAttributeToFilter($code,array('lteq'=>$value));  }
                        } elseif(substr_count($filter, '>=')) { 
                                list($code,$value) = explode('>=',$filter);
                                $this->_itemCollection->addAttributeToSelect($code, 'left');
                                if(substr_count($code, '_date')==0) $value = $value*1;
                                if($code && $value) {  $this->_itemCollection->addAttributeToFilter($code,array('gteq'=>$value));  }
                        } else { 
                                list($code,$value) = explode('=',$filter);
                                if($code == 'owner' && $value == 'customer_id' && is_object(Mage::getSingleton('customer/session'))) {
                                    $value = Mage::getSingleton('customer/session')->getCustomerId();
                                }
                                $value = explode('|',$value);

                                if(count($value) == 1) { 
                                        $value = $value[0];
                                        $this->_itemCollection->addAttributeToFilter(Mage::helper('score')->getLikeArray($code,$value));
                                } else { 
                                    $this->_itemCollection->addAttributeToFilter($code,array('in'=>$value));
                                }
                        }
                    }
                }
            }

		
        if (Mage::helper('score')->isModuleEnabled('Mage_Checkout')) {
            $this->_addOggettoAttributesAndPrices($this->_itemCollection);
        }

        $radius = Mage::getSingleton('customer/session')->getData('radius') * 1;
        $city = Mage::getSingleton('customer/session')->getData('city_dict');



        if(in_array($this->getSet(), array('Place','Event','Offer','OfferExtra'))) { 
            if($radius && $city && $oggetto->getCityDict()) { 
                    $this->_itemCollection->addAttributeToSelect('lat', 'left');
                    $this->_itemCollection->addAttributeToSelect('lng', 'left');
                    $this->_itemCollection->addAttributeToSelect('city_dict', 'left');
                    $city = Mage::getSingleton('core/resource')->getConnection('core_read')->query("SELECT * FROM city where id = ".$city)->fetchAll();
                    $cityLat = $city[0]['lat'];
                    $cityLong = $city[0]['long'];
                    
                    // TODO: instead of 'at_city_dict.value =' it's better to do at_city_dict.value LIKE expression here. But radius expression will cover needed objects anyway (if they have coordinates set)
                    $this->_itemCollection->getSelect()->where("(".(new Zend_Db_Expr ('(6371 * acos( cos( radians('.$cityLat.')) 
                                        * cos(radians(at_lat.value)) 
                                        * cos(radians('.$cityLong.') - radians(at_lng.value) ) + sin( radians('.$cityLat.') ) * sin( radians(at_lat.value) ) )
                                    )'))." <= ".$radius." OR at_city_dict.value = ".(Mage::getSingleton('customer/session')->getData('city_dict')).")");
            } elseif($city) {
                $this->_itemCollection->addAttributeToFilter(Mage::helper('score')->getLikeArray('city_dict',$city));
            }
        } 
        
        $this->_itemCollection->load();
		}

        if(!$this->_parentCollection) { 
		 $this->_parentCollection = $oggetto->getParentOggettoCollection()
            ->addAttributeToFilter('visibility',array('neq'=>1))
            ->addAttributeToSelect('*')
            ->addStoreFilter();

        if($this->getSet()) { 
            if(($this->getLocalise() && $this->getLocalise() != ',,,') && ($this->getSet()=='Place' || $this->getSet()=='Offer') && substr_count($this->getTemplate(), 'counter') && $oggetto->getAttributeSetId() == Mage::helper('score/oggetto')->getSetIdByCode('Idea')) { 
                    $counterArray = array();
                    if($oggetto->getShowPlaces()) $counterArray[] = Mage::helper('score/oggetto')->getSetIdByCode('Place');
                    if($oggetto->getShowOffers()) $counterArray[] = Mage::helper('score/oggetto')->getSetIdByCode('Offer');


                    $this->_parentCollection->addAttributeToFilter('attribute_set_id',array('in'=>$counterArray));
            } else { 
                $this->_parentCollection->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode($this->getSet()));
            }
        }

         if(isset($newFilters) && is_array($newFilters)) { 
                foreach($newFilters as $filter) {


                    if($filter) {
                        if(substr_count($filter, '<=')) { 
                                list($code,$value) = explode('<=',$filter);
                                 $this->_parentCollection->addAttributeToSelect($code, 'left');
                                if(substr_count($code, '_date')==0) $value = $value*1;
                                if($code && $value) {  $this->_parentCollection->addAttributeToFilter($code,array('lteq'=>$value));  }
                        } elseif(substr_count($filter, '>=')) { 
                                list($code,$value) = explode('>=',$filter);
                                $this->_parentCollection->addAttributeToSelect($code, 'left');
                                if(substr_count($code, '_date')==0) $value = $value*1;
                                if($code && $value) {  $this->_parentCollection->addAttributeToFilter($code,array('gteq'=>$value));  }
                        } else { 
                                list($code,$value) = explode('=',$filter);
                                if($code == 'owner' && $value == 'customer_id' && is_object(Mage::getSingleton('customer/session'))) {
                                    $value = Mage::getSingleton('customer/session')->getCustomerId();
                                }
                                $value = explode('|',$value);

                                if(count($value) == 1) { 
                                        $value = $value[0];
                                        $this->_parentCollection->addAttributeToFilter(Mage::helper('score')->getLikeArray($code,$value));
                                } else { 
                                    $this->_parentCollection->addAttributeToFilter($code,array('in'=>$value));
                                }
                        }
                    }
                }
            }


        if (Mage::helper('score')->isModuleEnabled('Mage_Checkout')) {
            $this->_addOggettoAttributesAndPrices($this->_parentCollection);
        }

      
        $city = Mage::getSingleton('customer/session')->getData('city_dict');

        if(!in_array($oggetto->getAttributeSetId(), array(44))) {  // exclude Excursion
            if(in_array($this->getSet(), array('Place','Event','Offer','OfferExtra'))) { 
                if($radius && $city && $oggetto->getCityDict()) { 
                        $this->_parentCollection->addAttributeToSelect('lat', 'left');
                        $this->_parentCollection->addAttributeToSelect('lng', 'left');
                        $this->_parentCollection->addAttributeToSelect('city_dict', 'left');
                        $city = Mage::getSingleton('core/resource')->getConnection('core_read')->query("SELECT * FROM city where id = ".$city)->fetchAll();
                        $cityLat = $city[0]['lat'];
                        $cityLong = $city[0]['long'];

                        // TODO: instead of 'at_city_dict.value =' it's better to do at_city_dict.value LIKE expression here. But radius expression will cover needed objects anyway (if they have coordinates set)
                        $this->_parentCollection->getSelect()->where("(".(new Zend_Db_Expr ('(6371 * acos( cos( radians('.$cityLat.')) 
                                            * cos(radians(at_lat.value)) 
                                            * cos(radians('.$cityLong.') - radians(at_lng.value) ) + sin( radians('.$cityLat.') ) * sin( radians(at_lat.value) ) )
                                        )'))." <= ".$radius." OR at_city_dict.value = ".(Mage::getSingleton('customer/session')->getData('city_dict')).")");

                    
                 
                } elseif($city) {
                    $this->_parentCollection->addAttributeToFilter(Mage::helper('score')->getLikeArray('city_dict',$city));
                }
            } 
        }
        // elseif(in_array($this->getSet(), array('Offer','OfferExtra'))) { 
        //     if($city) { 
        //          $this->_parentCollection->addAttributeToFilter(Mage::helper('score')->getLikeArray('city_dict',$city));
        //     }
        // }

        $this->_parentCollection->load();
        }

        foreach ($this->_itemCollection as $oggetto) {
            $oggetto->setDoNotUseCategoryId(true);
        }
		foreach ($this->_parentCollection as $oggetto) {
            $oggetto->setDoNotUseCategoryId(true);
        }

        return $this;
    }

    public function setCollection($collection) { 
         $this->_itemCollection = $collection;
         $this->_parentCollection = $collection; 
         return $this;
    }

    protected function _beforeToHtml()
    {
        $this->_prepareData();
        return parent::_beforeToHtml();
    }

    public function getItems()
    {
        return $this->_itemCollection;
    }
	
	public function getParentItems()
    {
        return $this->_parentCollection;
    }

    /**
     * Get tags array for saving cache
     *
     * @return array
     */
    public function getCacheTags()
    {
        return array_merge(parent::getCacheTags(), $this->getItemsTags($this->getItems()));
    }
}
