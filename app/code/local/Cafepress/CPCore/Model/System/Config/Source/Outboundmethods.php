<?php

class Cafepress_CPCore_Model_System_Config_Source_Outboundmethods extends Mage_Adminhtml_Model_System_Config_Source_Order_Status
{
     
     /**
      * Retrive order statuses as options for select
      *
      * @see Mage_Adminhtml_Model_System_Config_Source_Order_Status:toOptionArray()
      * @return array
      */
    public function toOptionArray()
    {
        return $this->getOutboundMethods();
    }
    
    private function getOutboundMethods()
    {
        return Mage::getModel('cpcore/xmlformat_outbound')->getAllMethods();
    }
}
