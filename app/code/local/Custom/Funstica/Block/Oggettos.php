<?php

class Custom_Funstica_Block_Oggettos extends Shaurmalab_Score_Block_Oggetto_All
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

            $queryText = $this->getRequest()->getParam('queryText');
            if($queryText) { 
                $queryText = '%' . trim(str_replace(' ', '%', $queryText)) . '%';
                $oggettos->addAttributeToFilter(array(
                    array('attribute'=> 'name','like' => $queryText),
                    array('attribute'=> 'short_descr','like' => $queryText)
                    )
                    );
            }
        
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
           
           if(isset($filtersData) && in_array('radius', array_keys($filtersData)) && isset($filtersData['city_dict']) && $filtersData['city_dict']) { 

                if($value) { 
                    $oggettos->addAttributeToSelect('lat', 'left');
                    $oggettos->addAttributeToSelect('lng', 'left');
                    $oggettos->addAttributeToSelect('city_dict', 'left');

                    for($i = 1; $i<=10; $i++) {
                        $oggettos->addExpressionAttributeToSelect(
                                'lat'.$i,
                                'SUBSTRING_INDEX(SUBSTRING_INDEX(at_lat.value, ",", '.$i.'), ",", -1)',
                                array())
                        ->addExpressionAttributeToSelect(
                                'lng'.$i,
                                'SUBSTRING_INDEX(SUBSTRING_INDEX(at_lng.value, ",", '.$i.'), ",", -1)',
                                array())
                        ;
                    }

                    $city = Mage::getSingleton('core/resource')->getConnection('core_read')->query("SELECT * FROM city where id = ".$filtersData['city_dict'])->fetchAll();
                    $cityLat = $city[0]['lat'];
                    $cityLong = $city[0]['long'];

                    $select = array();
                    for($i = 1; $i<=10; $i++) {
                        $select[] = '(6371 * 
                                        acos(
                                        cos(radians('.$cityLat.')) * cos(radians(SUBSTRING_INDEX(SUBSTRING_INDEX(at_lat.value, ",", '.$i.'), ",", -1))) 
                                        * cos(radians('.$cityLong.') - radians(SUBSTRING_INDEX(SUBSTRING_INDEX(at_lng.value, ",", '.$i.'), ",", -1))) 
                                        + sin(radians('.$cityLat.')) * sin(radians(SUBSTRING_INDEX(SUBSTRING_INDEX(at_lat.value, ",", '.$i.'), ",", -1)))
                                        )
                                    ) <= '.$filtersData['radius'];
                    }
                    $select = implode(' OR ', $select);

                    $oggettos->getSelect()->where(
                        $select." 
                        OR at_city_dict.value LIKE '%,".$filtersData['city_dict'].",%' 
                        OR at_city_dict.value LIKE '".$filtersData['city_dict'].",%' 
                        OR at_city_dict.value LIKE '%,".$filtersData['city_dict']."' 
                        OR at_city_dict.value = ".$filtersData['city_dict']);

                    unset($newFilters['radius']);
                    unset($newFilters['city_dict']);
                } else { 
                    unset($newFilters['radius']);
                }
            } else { 
                 if(in_array('radius', array_keys($filtersData))) unset($newFilters['radius']);
            
            }

#           echo $oggettos->getSelect(); die;
            
            $disabledCities =  Mage::getSingleton('core/resource')->getConnection('core_read')->query("SELECT * FROM city where store_id = 0")->fetchAll();
            $city_dict = array();
            foreach ($disabledCities as $city) {
                $city_dict[] = (int)$city['id'];
            }

           

            if(isset($newFilters) && is_array($newFilters)) { 
                foreach($newFilters as $filter) {


                    if($filter) {
                        if(substr_count($filter, '<=')) { 
                                list($code,$value) = explode('<=',$filter);
                                 $oggettos->addAttributeToSelect($code, 'left');
                                if(substr_count($code, '_date')==0) $value = $value*1;
                                if($code && $value) {  $oggettos->addAttributeToFilter($code,array('lteq'=>$value));  }
                        } elseif(substr_count($filter, '>=')) { 
                                list($code,$value) = explode('>=',$filter);
                                $oggettos->addAttributeToSelect($code, 'left');
                                if(substr_count($code, '_date')==0) $value = $value*1;
                                if($code && $value) {  $oggettos->addAttributeToFilter($code,array('gteq'=>$value));  }
                        } else { 
                                list($code,$value) = explode('=',$filter);
                                if($code == 'owner' && $value == 'customer_id' && is_object(Mage::getSingleton('customer/session'))) {
                                    $value = Mage::getSingleton('customer/session')->getCustomerId();
                                }
                                $value = explode('|',$value);

                                    
                                if(count($value) == 1  && !substr_count($code, 'category_id')) { 
                                        $value = $value[0];
                                        $oggettos->addAttributeToFilter(Mage::helper('score')->getLikeArray($code,$value));
                                } else { 
                                    if(substr_count($code, 'category_id')) { 
                                        if(!in_array('', $value)) { 
                                            $oggettos->addAttributeToFilter($code,array('in'=>$value));
                                        }
                                    } else { 
                                        $oggettos->addAttributeToFilter($code,array('in'=>$value));
                                    }

                                }
                        }
                    }
                }

                $blocked = array('city_dict'=>$city_dict);
                if($this->getSet()=='Idea') { 
                    $ids =  Mage::getModel('score/oggetto')->getCollection()
                        ->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode('ideacategory'))->addAttributeToFilter('hide_elements', 1)->getAllIds(); //array(40329, 40330, 40336, 40472, 40337, 40526);
                    if($ids) $blocked['ideacategory_id'] = $ids;
                }

                if($this->getSet()=='Place') { 
                    $ids =  Mage::getModel('score/oggetto')->getCollection()
                        ->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode('placecategory'))->addAttributeToFilter('hide_elements', 1)->getAllIds(); //array(40329, 40330, 40336, 40472, 40337, 40526);
                    if($ids) $blocked['placecategory_id'] = $ids;
                }

                if($this->getSet()=='Event') { 
                    $ids =  Mage::getModel('score/oggetto')->getCollection()
                        ->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode('eventcategory'))->addAttributeToFilter('hide_elements', 1)->getAllIds(); //array(40329, 40330, 40336, 40472, 40337, 40526);
                    if($ids) $blocked['eventcategory_id'] = $ids;
                }
                if($this->getSet()=='Travel') { 
                    $ids =  Mage::getModel('score/oggetto')->getCollection()
                        ->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode('travelcategory'))->addAttributeToFilter('hide_elements', 1)->getAllIds(); //array(40329, 40330, 40336, 40472, 40337, 40526);
                    if($ids) $blocked['travelcategory_id'] = $ids;
                }
                if($this->getSet()=='Discount') { 
                    $ids =  Mage::getModel('score/oggetto')->getCollection()
                        ->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode('discountcategory'))->addAttributeToFilter('hide_elements', 1)->getAllIds(); //array(40329, 40330, 40336, 40472, 40337, 40526);
                    if($ids) $blocked['discountcategory_id'] = $ids;
                }
                if($this->getSet()=='Interest') { 
                    $ids =  Mage::getModel('score/oggetto')->getCollection()
                        ->addAttributeToFilter('attribute_set_id',Mage::helper('score/oggetto')->getSetIdByCode('interestcategory'))->addAttributeToFilter('hide_elements', 1)->getAllIds(); //array(40329, 40330, 40336, 40472, 40337, 40526);
                    if($ids) $blocked['interestcategory_id'] = $ids;
                }


                foreach($blocked as $code => $value) { 
	                if(isset($newFilters[$code]) && substr_count($newFilters[$code], '=')) list($scode,$svalue) = explode('=',@$newFilters[$code]);    
			        if(!isset($newFilters[$code]) || (isset($newFilters[$code]) && in_array('', explode('|', $newFilters[$code]))) 
                        //|| (isset($newFilters[$code]) && !in_array($svalue, $blocked[$code]))
                        ) { 
                        $oggettos->addAttributeToSelect($code, 'left');
                 
                       $oggettos->addAttributeToFilter($code, array('nin'=> $value));
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
