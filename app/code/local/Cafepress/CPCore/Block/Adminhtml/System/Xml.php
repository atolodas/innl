<?php

class Cafepress_CPCore_Block_Adminhtml_System_Xml extends Mage_Adminhtml_Block_Template
{
    public function getInboundXmls()
    {
        return Mage::registry('found_inbound_xmls');
    }

    public function getOutboundXmls()
    {
        return Mage::registry('found_outbound_xmls');
    }

    public function getXmlDropdown()
    {
        $html = '';
        $html .= '<select id="file_inspector_dl" class="file_inspector_dl" name="options">';
        $html .= '<option label="-">-- Please select --</option>';
        $html .= '<optgroup label="Inbound">';
        foreach($this->getInboundXmls() as $_p){
            $html .= '<option value="'.$_p['file'].'">'.$_p['filename'].'</option>';
        }
        $html .= '</optgroup>';
        $html .= '<optgroup label="Outbound">';
        foreach($this->getOutboundXmls() as $_p){
            $html .= '<option value="'.$_p['file'].'">'.$_p['filename'].'</option>';
        }
        $html .= '</optgroup>';
        $html .= '</select>';
        return $html;
    }

    public function getFileName()
    {
        if($this->getRequest()->getParam('inbound')){
            $result = 'inbound/'.$this->getRequest()->getParam('inbound');
        }
        else if($this->getRequest()->getParam('outbound')){
            $result = 'outbound/'.$this->getRequest()->getParam('outbound');
        }
        else{
            $result = '';
        }
        return $result;
    }
}
