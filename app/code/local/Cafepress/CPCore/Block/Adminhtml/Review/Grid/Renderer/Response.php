<?php

class Cafepress_CPCore_Block_Adminhtml_Review_Grid_Renderer_Response extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $response = $row->getData($this->getColumn()->getIndex());
        return '<div class="wms-grid-request"><pre>'.htmlspecialchars($response).'</pre></div>';
    }
}

?>
