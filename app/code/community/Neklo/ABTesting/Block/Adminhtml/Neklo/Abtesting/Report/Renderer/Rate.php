<?php

class Neklo_ABTesting_Block_Adminhtml_Neklo_ABTesting_Report_Renderer_Rate
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $element = $row;
        
        $inits = (int)$element->getInitedNumber();
        $success = (int)$element->getEventsNumber();
        if($inits > 0) return round($success / $inits * 100, 2) . "%";
    	else return "0%";
    }
}
