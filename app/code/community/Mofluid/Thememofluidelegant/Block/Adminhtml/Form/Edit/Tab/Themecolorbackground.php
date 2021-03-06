<?php

class Mofluid_Thememofluidelegant_Block_Adminhtml_Form_Edit_Tab_Themecolorbackground extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * prepare form in tab
     */
    protected function _prepareForm()
    {
        $helper = Mage::helper('mofluid_thememofluidelegant');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_');
        $form->setFieldNameSuffix('mofluidtheme_background');
        
        $mofluid_theme_elegant_model = Mage::getModel('mofluid_thememofluidelegant/colors');
        $mofluid_theme_elegant = $mofluid_theme_elegant_model->getCollection()->addFieldToFilter('mofluid_theme_id','1')->addFieldToFilter('mofluid_color_type','background');
        $mofluid_theme_elegant_data = $mofluid_theme_elegant->getData(); 
        
        //creating forground fieldset tab
        $background_fieldset = $form->addFieldset('background', array(
            'legend'       => $helper->__('Background'),
            'class'        => 'fieldset-wide'
        ));
         foreach($mofluid_theme_elegant_data as $key=>$value) {
            $field_id = strtolower(str_replace(' ', '_', trim($value["mofluid_color_type"].'_'.$value["mofluid_color_id"])));
            $help_text = $value["mofluid_color_helptext"];
            if($value["mofluid_color_helplink"]) {
                $help_text .=  ' For More Detail : <a href="'.$value["mofluid_color_helplink"].'" target="_blank">Click Here</a>';
            }
            $background_fieldset->addField($field_id, 'text', array(
                'name'  => $field_id,
                'label' => $value["mofluid_color_label"],
                'value' => $value["mofluid_color_value"],
                'after_element_html' => $help_text,
                'required' => $value["mofluid_color_isrequired"] ? true : false,
                'class' => 'colorpicker'
            ));
        }
      
     if (Mage::registry('mofluid_thememofluidelegant')) {
            $form->setValues(Mage::registry('mofluid_thememofluidelegant')->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

}
