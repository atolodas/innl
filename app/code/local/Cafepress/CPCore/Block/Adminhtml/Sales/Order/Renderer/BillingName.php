<?php

class Cafepress_CPCore_Block_Adminhtml_Sales_Order_Renderer_BillingName extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $id = $row->getData('increment_id');

        $order = Mage::getModel('sales/order')->loadByIncrementId($id);
        $html = $order->getBillingAddress()->getFirstname().' '.$order->getBillingAddress()->getLastname();
        return $this->_getEscapedValue($html);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }
}

?>