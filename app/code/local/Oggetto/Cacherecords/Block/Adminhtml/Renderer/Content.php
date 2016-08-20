<?php

class Oggetto_Cacherecords_Block_Adminhtml_Renderer_Content extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	 $content = $row->getData($this->getColumn()->getIndex());
    	
    	return $this->_actionsToHtml($content);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        if($actions)
        { 
            return  'Content found';
        }
    }
}

?>