<?php

class Cafepress_CPWms_Lib_Varien_Data_Form_Element_OrdersGrid extends Varien_Data_Form_Element_Abstract
{
    protected $_attributes;
    
    public function __construct($attributes = array()){
        $this->_attributes = $attributes;
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml(){
        $ordersId = Mage::getSingleton('cpwms/xmlformat_format_order')->getOrdersId($this->_attributes['xmlformat_id']);
        $html = '<div id="orders_grid_content">
            Order Quantity: ';
        $html .= count($ordersId);
        $html .= '<select multiple="multiple" size="10">';
        foreach ($ordersId as $orderId) {
            $html .= '<option>'.$orderId.'</option>';
        }
        $html .= '</select></div>';
        return $html;
    }
    
}
