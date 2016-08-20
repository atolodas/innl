<?php

class Cafepress_CPWms_Lib_Varien_Data_Form_Element_ConditionsTable extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array()){
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml(){
        $format_id = Mage::registry('current_xmlformat')->getId();
        $html = "";
        if($format_id){
            $html .= "<script type='text/javascript'>
                    function getTable(){
                        new Ajax.Request('".Mage::getUrl('',array('_secure'=>true)).'cpwms/adminhtml_conditions/index/'."', {
                            method: 'GET',
                            parameters: {'format_id': Object.toJSON(".Mage::registry('current_xmlformat')->getId().")},
                            onComplete: function(response){
                                if(response.responseText){
//                                    alert(response.responseText.evalJSON().message);
                                    var condition_table_wrapper = document.getElementsByClassName('condition_table_wrapper')[0];
                                    condition_table_wrapper.innerHTML = response.responseText.evalJSON().message;
                                }
                            }
                        });
                    }
                </script>";
            $html .= "<button type='button' onclick='getTable()'><span>".Mage::helper('cpwms')->__('Get Table')."</span></button>";
            $html .= "<div class='condition_table_wrapper'>";
            $html .= "</div>";
        }
        return $html;
    }
}
