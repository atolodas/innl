<?php

class Cafepress_CPWms_Block_Adminhtml_Logging_Grid_Renderer_File extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
    	$file = $row->getData($this->getColumn()->getIndex());
    	$arr = explode(' ',$file);
    	$html = '';
    	foreach($arr as $file) {
            if($file) {
                if (strpos($file, '/')!=false){
                    $url = Mage::getBaseUrl().'cpwms'.DS.'adminhtml_viewxmls/index/'.$file;
                    $masF = explode('/',$file);
                    $file = end($masF);
                } else {
                    $url = Mage::getBaseUrl().'cpwms'.DS.'adminhtml_viewxmls/index/'.$file;
                    }
	    	$html.="<a href='".$url."'>".$file."</a><br/>";
            }
    	}
    	return $html;
    }

}

?>