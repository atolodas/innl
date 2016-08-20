<?php

class Cafepress_CPWms_Lib_Varien_Data_Form_Element_CPPrints extends Varien_Data_Form_Element_Abstract
{
    public function __construct($attributes = array()){
        parent::__construct($attributes);
        $this->setType('label');
    }

    public function getElementHtml(){
        $html = '';

        $prints_dir = opendir(Mage::getBaseDir('media').'/cafepress/prints');
        $html .= '<div class="cp_create_prints_wrapper"><table class="cp_create_prints">';
        $col = 0;
        $col_max = 4;
        while(($file = readdir($prints_dir)) !== false){
            $filepath = Mage::getBaseDir('media').'/cafepress/prints/'.$file;
            if(filetype($filepath) == 'file'){
                if($col == 0){
                    $html .= '<tr>';
                }
                $html .= '
                    <td>
                        <div class="cp_create_print" onclick="selectPrint(this)">
                            <img width="150" height="150" src="'.Mage::getBaseUrl('media').'cafepress/prints/'.$file.'">
                            <input class="cp_print_id" type="hidden" value="'.$file.'">
                        </div>
                    </td>';
                $col++;
                if($col >= $col_max){
                    $html .= '</tr>';
                    $col = 0;
                }
            }
        }
        $html .= '</table></div>';

        return $html;
    }
}