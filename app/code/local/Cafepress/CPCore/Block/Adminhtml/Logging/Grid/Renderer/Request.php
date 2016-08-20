<?php

class Cafepress_CPCore_Block_Adminhtml_Logging_Grid_Renderer_Request extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$log_id = $row->getData($this->getColumn()->getIndex());
        
        $url = $this->getUrl('*/adminhtml_logging/request', array('log_id' => $log_id));

        $html = "<a href='$url'>view</a><br/>";
    	return $html;
    }

}

?>