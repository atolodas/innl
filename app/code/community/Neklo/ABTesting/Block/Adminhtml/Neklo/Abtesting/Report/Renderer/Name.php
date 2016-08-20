<?php

class Neklo_ABTesting_Block_Adminhtml_Neklo_ABTesting_Report_Renderer_Name
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $element = $row;
        
        $tests = $element->getTests();
        
        $name = ucfirst(str_replace(array('=',',','AB_test_','_'), array(': ', ' <br/> ','',' '), $tests));

        return $name;
    }
}
