<?php

abstract class Cafepress_CPCore_Model_Xmlformat_Entity_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Global Variables For Wms Entityes
     */
    protected $_orderId = false;
    protected $_order = false;
    
    protected function registry($key, $value){
        if (Mage::registry($key)){
            Mage::unregister($key);
        }
        Mage::register($key, $value);
    }


    public function reset(){
        $this->resetOrder();
        return $this;
    }
    
    public function resetOrder(){
        $this->_orderId = false;
        $this->_order = false;
        
        Mage::unregister('wms_entity_current_order');
        Mage::unregister('wms_entity_current_order_id');
        return $this;
    }

    public function setOrderId($orderId){
        $this->_orderId = $orderId;
        $this->registry('wms_entity_current_order_id', $orderId);
        return $this;
    }
    
    public function getOrderId(){
        if (!$this->_orderId && Mage::registry('wms_entity_current_order_id')){
            return Mage::registry('wms_entity_current_order_id');
        }
        return $this->_orderId;
    }
    
    public function getOrder(){
        if (!Mage::registry('wms_entity_current_order') && $this->getOrderId()){
            $order = Mage::getModel("sales/order")->loadByIncrementId($this->getOrderId());
            if ($order->getId()){
                $this->_order = $order;
                Mage::register('wms_entity_current_order',$order);
            } 
        }
        return $this->_order;
    }
    
  

    
}

