<?php
class Cafepress_CPWms_Block_Adminhtml_Sales_Order_View_Wmsstatus  extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
  public function getCurrentStatus()
    {
        return $this->getOrder()->getWmsFileStatus();
    }
}
?>