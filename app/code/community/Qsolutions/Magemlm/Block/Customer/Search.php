<?php

/**
 * Magemlm
 *
 * @category    Qsolutions
 * @package     Qsolutions_Magemlm
 * @copyright   Copyright (c) 2013 Q-Solutions  (http://www.qsolutions.com.pl)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Qsolutions_Magemlm_Block_Customer_Search extends Shaurmalab_Score_Block_Oggetto_List {

	/**
     * Constructor. Set template.
     */
  public function __construct() {
    parent::__construct();
    $this->setTemplate("magemlm/search.phtml") ;
  }

  public function getCustomers() {

    $customers = Mage::getModel('customer/customer')->getCollection()
    ->addNameToSelect()
    ->addAttributeToFilter('store_id',Mage::app()->getStore()->getId())
    ->addAttributeToSelect('*')
    ;
    if(isset($_GET) && !empty($_GET)) {
      foreach($_GET as $k=>$v) {
        if(in_array($k , array('___store','p','limit','sort','dir'))) continue;
        try { 
        if(is_array($v)) { 
          foreach($v as $val)
          {
            $condArr[] = array($val);
            $condArr[] = array('like' =>$val. ',%');
            $condArr[] = array('like' => '%,' . $val);
            $condArr[] = array('like' => '%,' . $val . ',%');
          }
          try { 
            $customers->addAttributeToFilter($k,$condArr);
          } catch(Exception $e) {}
        } else { 
          $v = str_replace(' ','%',$v);
          try { 
            $customers->addAttributeToFilter($k,array('like'=>'%'.$v.'%'));
          } catch(Exception $e) {}
        }
        } catch (Exception $e) { }
      }
    }
    return $customers;
  }

  protected function _getOggettoCollection()
  {
    if (is_null($this->_oggettoCollection)) {
      $layer = $this->getLayer();
      $this->_oggettoCollection = $this->getCustomers();
    }

    return $this->_oggettoCollection;
  }
}
