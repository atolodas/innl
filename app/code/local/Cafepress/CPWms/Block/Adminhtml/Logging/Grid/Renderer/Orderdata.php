<?php

class Cafepress_CPWms_Block_Adminhtml_Logging_Grid_Renderer_Orderdata extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $html = '';
    	$order_id = $row->getData($this->getColumn()->getIndex());
        if (!is_numeric($order_id)){
            return  $html;
        }
        
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        if (!$order->getId()){
            return  $html;
        }
        
        $order_wms_file_status = $order->getWmsFileStatus();
        if ($order_wms_file_status){
            $html = '';
            if (is_numeric($order_wms_file_status)){
                $statuses = Mage::helper('cpwms')->getStatuses();
                $html .= $statuses[$order_wms_file_status].'<br/>';
            } else {
                $filestatuses = unserialize($order_wms_file_status);
                foreach ($filestatuses as $format => $filestatus)
                {
                    $html .= $format.':'.$filestatus.'<br/>';
                }
            }
        }
        
        $order_wms_file = $order->getWmsFile();
        if($order_wms_file){
            $arr = explode(' ',$order_wms_file);
            foreach($arr as $file) {
                if($file) {
                    if (strpos($file, '/')!=false){
                        $url = '/index.php/cpwms'.DS.'adminhtml_viewxmls/index/outbound/'.$file;//Mage::getBaseUrl('media').'xmls/'.$file;
                        
                        $masF = explode('/',$file);
                        $file = end($masF);
                    } else {
                        $url = '/index.php/cpwms'.DS.'adminhtml_viewxmls/index/outbound/'.$file;//Mage::getBaseUrl('media').'xmls/outbound/'.$file;
                        }
                $html.="<a href='".$url."'>".$file."</a><br/>";
                }
            }
        }
        
        if($order->hasCreditmemos()) {
           foreach($order->getCreditmemosCollection() as $creditmemo) {
                if($file = trim($creditmemo->getWmsFile())) {
                    if (strpos($file, '/')!=false){
                        $url = '/index.php/cpwms'.DS.'adminhtml_viewxmls/index/outbound/'.$file;//Mage::getBaseUrl('media').'xmls/'.$file;
                    } else {
                        $url = '/index.php/cpwms'.DS.'adminhtml_viewxmls/index/outbound/'.$file;//Mage::getBaseUrl('media').'xmls/outbound/'.$file;
                    }
                    $html.="<a href='".$url."'>".$file."</a><br/>";
                }
           }
         }

//        $html = "<a href='#'>show</a><br/>";
    	return $html;
    }

}

?>