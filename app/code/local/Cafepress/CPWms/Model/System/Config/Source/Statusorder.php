<?php

 class Cafepress_CPWms_Model_System_Config_Source_Statusorder extends Mage_Adminhtml_Model_System_Config_Source_Order_Status
 {
     private $spliseOptions = array(
         'canceled',
         'closed',
//          'holded',
     );
     
     /**
      * Retrive order statuses as options for select
      *
      * @see Mage_Adminhtml_Model_System_Config_Source_Order_Status:toOptionArray()
      * @return array
      */
    public function toOptionArray()
    {
        $options = parent::toOptionArray();
        array_shift($options); // Remove '--please select--' option
        $optionsOut = array();
        foreach ($options as $key =>$val){
            if (array_search($val['value'], $this->spliseOptions, true)===FALSE){
                $optionsOut[$key] = $options[$key];
            }
        }
        return $optionsOut;
    }
 }
