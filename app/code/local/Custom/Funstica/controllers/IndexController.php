<?php
class Custom_Funstica_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
    * Index action
    */
    public function indexAction()
    {
        $this->_redirect('/');
    }

    public function getObjectsForHomepageAction() { 
    	$params = Mage::app()->getRequest()->getParams();
    	$country = @$params['country_dict'];
    	$city = @$params['city_dict'];
        $radius = @$params['radius'];
        
        foreach ($params as $key => $value) {
            if(!is_array($value)) { 
                $this->_getSession()->setData($key,$value);
            }
        }
        if(!$this->_getSession()->getData('radius')) $this->_getSession()->setData('radius',20);

        $filters = array();
        if($country) { 
            $filters[] = "country_dict={$country}"; 
           
        } else { 
            $id = Mage::helper('score/oggetto')->isDictionaryAttribute('country_dict');
             $elements = Mage::helper('score/oggetto')->getDictionaryValues($id);
             $ids = array();
            foreach ($elements as $object) {
               $ids[] = $object['id'];
            }
            if(!empty($ids))  $filters[] = "country_dict=".implode('|',$ids);
        }

        if($city) { 
            $filters[] = "city_dict={$city}";
        }

        $price = @$params['fprice'];
        if($price) { 
            $price = (int)$price;
		$price = $price / 1000;
            $filters[] = "fprice<={$price}";
        }

        $filters[] = "visibility>=2";
        $filters[] = "search_visibility=1";

        $filter = implode(',',$filters);

  if($params['start_date']['from'])  $this->_getSession()->setStartDate($params['start_date']['from']);
       
        $dates = Mage::helper('score')->filterDates(array('start_date'=>@$params['start_date']['from']));
        $date = @$dates['start_date'];


        $keys = array_keys($params);
        if(!in_array('discountcategory_id', $keys) &&  !in_array('ideacategory_id', $keys) && !in_array('placecategory_id', $keys) && !in_array('eventcategory_id', $keys)  && !in_array('interestcategory_id', $keys)  && !in_array('travelcategory_id', $keys)) { 

        $data['ideas'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
    					->setSet("Idea")->setDefaultMode("list")
    					->setPrefilter("interest=1,{$filter}")
    					->setOnlyPublic("1")->setToolbar("0")
    					->setPager("0")
                        ->setNewlimit(7)->setNewpage(1)
    					->setTemplate("score/oggetto/list.phtml")
    					->toHtml();

        $data['ideas'].= Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Idea")->setDefaultMode("list")
                        ->setPrefilter("{$filter}")
                        ->setOnlyPublic("1")
                        ->setTemplate("score/oggetto/list_counter.phtml")
                        ->toHtml();

        $data['travels'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Travel")->setDefaultMode("list")
                        ->setPrefilter("interest=1,{$filter}")
                        ->setOnlyPublic("1")->setToolbar("0")
                        ->setPager("0")
                        ->setNewlimit(7)->setNewpage(1)
                        ->setTemplate("score/oggetto/list.phtml")
                        ->toHtml();

        $data['travels'].= Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Travel")->setDefaultMode("list")
                        ->setPrefilter("{$filter}")
                        ->setOnlyPublic("1")
                        ->setTemplate("score/oggetto/list_counter.phtml")
                        ->toHtml();

        $data['interests'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Interest")->setDefaultMode("list")
                        ->setPrefilter("interest=1,{$filter}")
                        ->setOnlyPublic("1")->setToolbar("0")
                        ->setPager("0")
                        ->setNewlimit(7)->setNewpage(1)
                        ->setTemplate("score/oggetto/list.phtml")
                        ->toHtml();

        $data['interests'].= Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Interest")->setDefaultMode("list")
                        ->setPrefilter("{$filter}")
                        ->setOnlyPublic("1")
                        ->setTemplate("score/oggetto/list_counter.phtml")
                        ->toHtml();

        $data['discounts'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Discount")->setDefaultMode("list")
                        ->setPrefilter("interest=1,{$filter}")
                        ->setOnlyPublic("1")->setToolbar("0")
                        ->setPager("0")
                        ->setNewlimit(7)->setNewpage(1)
                        ->setTemplate("score/oggetto/list.phtml")
                        ->toHtml();

        $data['discounts'].= Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Discount")->setDefaultMode("list")
                        ->setPrefilter("{$filter}")
                        ->setOnlyPublic("1")
                        ->setTemplate("score/oggetto/list_counter.phtml")
                        ->toHtml();

               $filter.=",radius<=20";
    	$data['places'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
    					->setSet("Place")->setDefaultMode("list")
    					->setPrefilter("interest=1,{$filter}")
    					->setOnlyPublic("1")->setToolbar("0")
    					->setPager("0")
                        ->setNewlimit(7)->setNewpage(1)
    					->setTemplate("score/oggetto/list.phtml")
    					->toHtml();

        $data['places'].= Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Place")->setDefaultMode("list")
                        ->setPrefilter("{$filter}")
                        ->setOnlyPublic("1")
                        ->setTemplate("score/oggetto/list_counter.phtml")
                        ->toHtml();

    	$data['events'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
    					->setSet("Event")->setDefaultMode("list")
    					->setPrefilter("interest=1,{$filter},start_date>={$date}")
    					->setOnlyPublic("1")->setToolbar("0")
    					->setPager("0")
    					->setNewSort('start_date')
					->setNewSortDir('asc')
					->setNewlimit(7)->setNewpage(1)
    					->setTemplate("score/oggetto/list.phtml")
    					->toHtml();

        $data['events'].= Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Event")->setDefaultMode("list")
                        ->setPrefilter("{$filter},start_date>={$date}")
                        ->setOnlyPublic("1")
                        ->setTemplate("score/oggetto/list_counter.phtml")
                        ->toHtml();



        }  
    
        if($country)  $this->_getSession()->setCountryDict($country);
        if($city)  $this->_getSession()->setCityDict($city);
        if(isset($params['p'])) $this->_getSession()->setData($keys[1].'-page',$params['p']);
        if($radius) $this->_getSession()->setData('radius',$radius);

        foreach ($params as $key => $value) {
            if(substr_count($key, 'category_id')) { 
                $this->_getSession()->setData($key, $value);
                $params[$key] = implode('|', $value);
            }
        }
        $sort  = @$params['sort'];

        if(!$sort) $sort = 'likes_counter';
        $sortDir = 'desc';
        if($sort == 'fprice' || $sort == 'start_date') { 
            $sortDir = 'asc';
        }

        $this->_getSession()->setSort($sort);

        if(in_array('ideacategory_id', $keys)) { 
            $category = $params['ideacategory_id'];

            $this->_getSession()->setIdeacategoryId($category);

           if($category && !in_array('', $params['ideacategory_id']))  $filter.=",ideacategory_id={$category}";
           if($radius) { 
               $filter.=",radius<={$radius}";
            }
            $data['ideas'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Idea")
                        ->setPrefilter($filter)
                        ->setOnlyPublic("1")
                        ->setNewSort($sort)
                        ->setNewSortDir($sortDir)
                        ->setTemplate("score/oggetto/list.phtml")
                        ->toHtml();

        }

         if(in_array('discountcategory_id', $keys)) { 
            $category = $params['discountcategory_id'];
             $this->_getSession()->setDiscountcategoryId($category);

            if($category) $filter.=",discountcategory_id={$category}";
            $data['discounts'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Discount")
                        ->setPrefilter($filter)
                        ->setOnlyPublic("1")
                        ->setNewSort($sort)
                        ->setNewSortDir($sortDir)
                        ->setTemplate("score/oggetto/list.phtml")
                        ->toHtml();

        }

         if(in_array('placecategory_id', $keys)) { 
            $category = $params['placecategory_id'];
             $this->_getSession()->setPlacecategoryId($category);

            if($category) $filter.=",placecategory_id={$category}";
            if($radius) { 
               $filter.=",radius<={$radius}";
            }
            $data['places'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Place")
                        ->setPrefilter($filter)
                        ->setOnlyPublic("1")
                        ->setNewSort($sort)
                        ->setNewSortDir($sortDir)
                        ->setTemplate("score/oggetto/list.phtml")
                        ->toHtml();
        }

        if(in_array('eventcategory_id', $keys)) { 
            $category = $params['eventcategory_id'];
             $this->_getSession()->setEventcategoryId($category);

            $filter = "{$filter},start_date>={$date}";
            if($category) $filter.=",eventcategory_id={$category}";
            if($radius) { 
               $filter.=",radius<={$radius}";
            }
            $data['events'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Event")
                        ->setPrefilter($filter)
                        ->setOnlyPublic("1")
                        ->setNewSort($sort)
                        ->setNewSortDir($sortDir)
                        ->setTemplate("score/oggetto/list.phtml")
                        ->toHtml();
        }

         if(in_array('interestcategory_id', $keys)) { 
            $category = $params['interestcategory_id'];
             $this->_getSession()->setInterestcategoryId($category);

            if($category) $filter.=",interestcategory_id={$category}";
            $data['interests'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Interest")
                        ->setPrefilter($filter)
                        ->setOnlyPublic("1")
                        ->setNewSort($sort)
                        ->setNewSortDir($sortDir)
                        ->setTemplate("score/oggetto/list.phtml")
                        ->toHtml();
        }

            if(in_array('travelcategory_id', $keys)) { 
            $category = $params['travelcategory_id'];
             $this->_getSession()->setTravelcategoryId($category);

            if($category) $filter.=",travelcategory_id={$category}";
            $data['travels'] = Mage::app()->getLayout()->createBlock("score/oggetto_all")
                        ->setSet("Travel")
                        ->setPrefilter($filter)
                        ->setOnlyPublic("1")
                        ->setNewSort($sort)
                        ->setNewSortDir($sortDir)
                        ->setTemplate("score/oggetto/list.phtml")
                        ->toHtml();
        }


        if(!isset($data['ideas']) || !trim($data['ideas'])) $data['ideas'] = $this->__('No Data Found');
        if(!isset($data['places']) || !trim($data['places'])) $data['places'] = $this->__('No Data Found');
        if(!isset($data['events']) ||  !trim($data['events'])) $data['events'] = $this->__('No Data Found');
        if(!isset($data['interests']) || !trim($data['interests'])) $data['interests'] = $this->__('No Data Found');
        if(!isset($data['travels']) || !trim($data['travels'])) $data['travels'] = $this->__('No Data Found');
        if(!isset($data['discounts']) || !trim($data['discounts'])) $data['discounts'] = $this->__('No Data Found');
    	
        Mage::app()->getResponse()->setBody(json_encode($data));
    }

    public function getCitiesByCountryIdAction() { 
            $params = Mage::app()->getRequest()->getParams();
            $country = $params['countryId'];
            $cities = array();
            $query = "SELECT * FROM city where country_id = :country_id and  store_id = :store_id";
            $bind = array('country_id' => $country,   'store_id' => Mage::app()->getStore()->getId());
            $citiesObjects = Mage::getSingleton('core/resource')->getConnection('core_read')->query($query,$bind)->fetchAll();
               $ids[] = '='.(Mage::helper('customer')->__('All cities')).'='.(Mage::helper('customer')->__('All cities'));
          
            foreach ($citiesObjects as $city) {
                $ids[] = $city['id'].'='.$city['title'].'='.(Mage::helper('score/oggetto_url')->format($city['title']));
            }
            $cities = implode(',', $ids);
            Mage::register('cities'.$country,$cities);
            echo $cities;
    }

    public function getCitiesByCountryCodeAction() { 
            $params = Mage::app()->getRequest()->getParams();
            $country = strtolower($params['countryCode']);

            $query = "SELECT * FROM country where code = :code";
            $bind = array('code' => $country);
            $countryObjects = Mage::getSingleton('core/resource')->getConnection('core_read')->query($query,$bind)->fetchAll();
            if(!isset($countryObjects[0]) || !isset($countryObjects[0]['id'])) return '';
            $country = $countryObjects[0]['id'];

            $cities = array();
            $query = "SELECT * FROM city where country_id = :country_id and  store_id = :store_id";
            $bind = array('country_id' => $country,   'store_id' => Mage::app()->getStore()->getId());
            $citiesObjects = Mage::getSingleton('core/resource')->getConnection('core_read')->query($query,$bind)->fetchAll();
            foreach ($citiesObjects as $city) {
                $ids[] = $city['id'].'='.$city['title'].'='.(Mage::helper('score/oggetto_url')->format($city['title']));
            }
            $cities = implode(',', $ids);
            Mage::register('cities'.$country,$cities);
            echo $cities;
    }

    public function newsfeedAction() { 

         if (!$this->_getSession()->isLoggedIn()) {
            $this->_redirect('customer/account/login');
            return;
        } else { 
            $this->loadLayout();
            $this->_initLayoutMessages('customer/session');
            $this->_initLayoutMessages('catalog/session');

            $this->getLayout()->getBlock('head')->setTitle($this->__('Newsfeed'));
            $this->renderLayout();
        }
    }

       /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}
