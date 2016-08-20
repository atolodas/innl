<?php

class Cafepress_CPWms_Block_Adminhtml_Sales_Order_Renderer_Wmsfile extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$files = $row->getData($this->getColumn()->getIndex());
        $html = '';
        
        if($files){
            $arr = explode(' ',$files);
            foreach($arr as $file) {
                if($file) {
                    if (strpos($file, '/')!=false){
                        $masF = explode('/',$file);
                        $url = Mage::getBaseUrl().'cpwms'.DS.'adminhtml_viewxmls/index/'.$file;
                        $file = end($masF);
                    } else {
                        $url = Mage::getBaseUrl().'cpwms'.DS.'adminhtml_viewxmls/index/outbound/'.$file;
                        }
                $html.="<a href='".$url."'>".$file."</a><br/>";
                }
            }
        }
        
         $order = Mage::getModel('sales/order')->loadByIncrementId($row->getData('increment_id'));
         if($order->hasCreditmemos()) {
           foreach($order->getCreditmemosCollection() as $creditmemo) {
                if($file = trim($creditmemo->getWmsFile())) {
                    if (strpos($file, '/')!=false){
                        $url = Mage::getBaseUrl().'cpwms'.DS.'adminhtml_viewxmls/index/'.$file;
                        $masF = explode('/',$file);
                        $file = end($masF);
                    } else {
                        $url = Mage::getBaseUrl().'cpwms'.DS.'adminhtml_viewxmls/index/outbound/'.$file;
                    }
                    $html.="<a href='".$url."'>".$file."</a><br/>";
                }
           }
         }
    	return $html;
    }

    protected function _getEscapedValue($value)
    {
        return addcslashes(htmlspecialchars($value),'\\\'');
    }


}

?>