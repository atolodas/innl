<?php

class Cafepress_CPCore_Block_Adminhtml_Review_Grid_Renderer_Url extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$formatId = $row->getData($this->getColumn()->getIndex());
    	$itemStoreId = $row->getData('store_id');
        if ($itemStoreId){
            $storeId = $itemStoreId;
        } else {
            $storeId = (int)$this->getRequest()->getParam('store', 0);
        }
        
        
        $format = Mage::getSingleton('cpcore/xmlformat')->load($formatId);
        $formatType = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getNameTypeById($format->getType());
        $formatModel = Mage::getModel('cpcore/xmlformat_format_'.$formatType)
                ->setStoreId($storeId)
                ->load($formatId);
        $url = $formatModel->getUrlOfRequest();
//        $methods = $formatModel->getMethodOfRequest();
        
        $html = '';
        foreach($url as $method=> $url){
            $html .="$method:<br/>$url<br/><br/>";
        }
        
        return $html;
    	
    }

}

?>