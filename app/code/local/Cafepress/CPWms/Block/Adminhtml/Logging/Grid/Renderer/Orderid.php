<?php

class Cafepress_CPWms_Block_Adminhtml_Logging_Grid_Renderer_Orderid extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$id = $row->getData($this->getColumn()->getIndex());
        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            $url = $this->getUrl('admin/sales_order/view', array('order_id' => $id));
            $html = "<a href='".$url."'>".$id."</a><br/>";
        } else {
            $html = $id;
        }
    	
//        $html="<a href='".$url."'>".$file."</a><br/>";
    	return $html;
    }

}

?>