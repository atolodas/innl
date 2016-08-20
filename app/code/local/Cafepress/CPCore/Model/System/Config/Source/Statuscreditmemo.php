<?php

class Cafepress_CPCore_Model_System_Config_Source_Statuscreditmemo extends Mage_Adminhtml_Model_System_Config_Source_Order_Status
{
     
     private $spliseOptions = array(
         'canceled',
//         'holded',
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
//        Zend_Debug::dump($options);
        foreach ($options as $key =>$val){
            if (array_search($val['value'], $this->spliseOptions, true)===FALSE){
                $optionsOut[$key] = $options[$key];
            }
        }
        return $optionsOut;
    }
}
