<?php

class Shaurmalab_Score_Block_Oggetto_All extends Shaurmalab_Score_Block_Oggetto_List
{
  public $set;
  public $collectionSize;
  public function getSetId()
	{
      $setId = Mage::helper('score/oggetto')->getSetIdByCode($this->getSet());
      return $setId;
  }

    public function getOggettos()
    {
        $oggettos = array();

        $filters = $this->getPrefilter();
        $filters = explode(',',$filters);
        $predefined = array();

        
            $oggettos = Mage::getModel('score/oggetto')->getCollection()
                ->addAttributeToFilter('attribute_set_id',$this->getSetId())
		        ->addStoreFilter()
                ->addAttributeToSelect('*')
                ;
        
            if($this->getOnlyPublic()) {
                $oggettos->addAttributeToFilter('is_public','1');
            }

           if($this->getNewSort()) {
                  $oggettos->addAttributeToSort($this->getNewSort(), (($this->getNewSortDir())?$this->getNewSortDir():'desc'));
           } 
            $oggettos->addAttributeToSort('created_at', 'desc');
           


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
            
           if(isset($filtersData) && is_array($filtersData)) { 
               if(in_array('radius', array_keys($filtersData)) && isset($filtersData['city_dict']) && $filtersData['city_dict']) { 

                    if($value) { 
                        $this->_itemCollection->addAttributeToSelect('lat', 'left');
                        $this->_itemCollection->addAttributeToSelect('lng', 'left');
                        $oggettos->addAttributeToSelect('city_dict', 'left');
                        $city = Mage::getSingleton('core/resource')->getConnection('core_read')->query("SELECT * FROM city where id = ".$filtersData['city_dict'])->fetchAll();
                        $cityLat = $city[0]['lat'];
                        $cityLong = $city[0]['long'];
                        $oggettos->getSelect()->where("(".(new Zend_Db_Expr ('(6371 * acos( cos( radians('.$cityLat.')) 
                                            * cos(radians(at_lat.value)) 
                                            * cos(radians('.$cityLong.') - radians(at_lng.value) ) + sin( radians('.$cityLat.') ) * sin( radians(at_lat.value) ) )
                                        )'))." <= ".$filtersData['radius']." OR at_city_dict.value = ".$filtersData['city_dict'].")");
                        
                        unset($newFilters['radius']);
                        unset($newFilters['city_dict']);

                    } else { 
                        unset($newFilters['radius']);
                    }
                } else { 
                     if(in_array('radius', array_keys($filtersData))) unset($newFilters['radius']);
                
                }
            }

            //echo $oggettos->getSelect();
            if(isset($newFilters) && is_array($newFilters)) { 
                foreach($newFilters as $filter) {
                    if($filter) {
                        if(substr_count($filter, '<=')) { 
                            list($code,$value) = explode('<=',$filter);
                                 $value = $value*1;
                            if($code && $value) {  $oggettos->addAttributeToFilter($code,array('lteq'=>$value));  }
                        } elseif(substr_count($filter, '>=')) { 
                            list($code,$value) = explode('>=',$filter);
                                 $value = $value*1;
                                 if($code && $value) {  $oggettos->addAttributeToFilter($code,array('gteq'=>$value));  }
                        } else { 
                                list($code,$value) = explode('=',$filter);
                                if($code == 'owner' && $value == 'customer_id' && is_object(Mage::getSingleton('customer/session'))) {
                                    $value = Mage::getSingleton('customer/session')->getCustomerId();
                                }
                                $value = explode('|',$value);
                                if(count($value) == 1) { 
                                    $value = $value[0];
                                     $oggettos->addAttributeToFilter(Mage::helper('score')->getLikeArray($code,$value));
                                } else { 
                                    $oggettos->addAttributeToFilter($code,array('in'=>$value));
                                }
                        }
                    }
                }
            }

            $this->collectionSize = $oggettos->getSize();
            
            $p = 1;
            if($this->getNewlimit()) {
                $limit = $this->getNewlimit();
                $p = $this->getNewpage();
                if(isset($_GET['p'])) $p = $_GET['p'];
                Mage::app()->getRequest()->setParam('limit',$limit);
                if($p) {
                    Mage::app()->getRequest()->setParam('p',$p);
                }
            }
//echo $oggettos->getSelect();
        return $oggettos;
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
}
