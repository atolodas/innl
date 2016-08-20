<?php

class Oggetto_Cacherecords_Block_Adminhtml_Renderer_Url extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	 $url = str_replace('&',' &',$row->getData($this->getColumn()->getIndex()));
    	$actions[] = array('url' =>  $url);
    	return $this->_actionsToHtml($actions);
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }

    protected function _actionsToHtml(array $actions)
    {
        $html = array();
        $attributesObject = new Varien_Object();

        foreach ($actions as $action) {
           	$html[] = $action['url'];
        }
        return  substr(implode($html),0,80);
    }
}

?>