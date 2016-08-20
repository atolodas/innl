<?php

class Cafepress_CPCore_Block_Adminhtml_Sales_Order_Renderer_Wmsfilestatus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row)
	{
        $index_data = $row->getData($this->getColumn()->getIndex());
        $html = '';
        
        if (is_numeric($index_data)){
            $statuses = Mage::helper('cpcore')->getStatuses();
            if (isset($statuses[$index_data])){
                $html .= 'last:'.$statuses[$row->getData($this->getColumn()->getIndex())];
            } else {
                $html .= 'last:#'.$index_data;
            }
            
        } elseif ($index_data) {
            $filestatuses = unserialize($index_data);
            foreach ($filestatuses as $format => $filestatus)
			{
				$html .= $format.':'.$filestatus.'<br/>';
			}
        }
        
//		$filestatuses = unserialize($row->getData($this->getColumn()->getIndex()));
//		$html = '';
//		$statuses = Mage::helper('cpcore')->getStatuses();
//		if(!$filestatuses) $html .= 'last:'.$statuses[$row->getData($this->getColumn()->getIndex())];
//		else
//		{
//			foreach ($filestatuses as $format => $filestatus)
//			{
//				$html .= $format.':'.$filestatus.'<br/>';
//			}
//		}
		return $html;
	}

	protected function _getEscapedValue($value)
	{
		return addcslashes(htmlspecialchars($value),'\\\'');
	}
}

?>
