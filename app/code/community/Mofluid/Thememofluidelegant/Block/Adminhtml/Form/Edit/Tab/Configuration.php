<?php

class Mofluid_Thememofluidelegant_Block_Adminhtml_Form_Edit_Tab_Configuration extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * prepare form in tab
     */
    protected function _prepareForm()
    {
    
        $helper = Mage::helper('mofluid_thememofluidelegant');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_');
        $form->setFieldNameSuffix('mofluidtheme_config');

        $mofluid_theme_elegant_model = Mage::getModel('mofluid_thememofluidelegant/thememofluidelegant');
        $mofluid_theme_elegant = $mofluid_theme_elegant_model->getCollection()->addFieldToFilter('mofluid_theme_id','1');
        $mofluid_theme_elegant_data = $mofluid_theme_elegant->getData(); 
        
        $elegant_theme_settings = $mofluid_theme_elegant_data[0]; 
        $configuration_fieldset = $form->addFieldset('configuration', array(
            'legend'       => $helper->__('Configuration'),
            'class'        => 'fieldset-wide',
            'expanded'  => false,
        ));
        
        $configuration_fieldset->addField('mofluid_theme_id', 'hidden', array(
          'name'      => 'mofluid_theme_id',
          'value'     => $elegant_theme_settings['mofluid_theme_id'],
        ));
       
        $configuration_fieldset->addField('mofluid_theme_catsimg', 'select', array(
          'label'     => $helper->__('Display Category Images'),
          'name'      => 'mofluid_theme_catsimg',
          'required'  => true,
          'value'     => $elegant_theme_settings['mofluid_display_catsimg'],
          'after_element_html' => '<br>Enable if you want to display category thumbnail images on listing.For More Detail : <a href="http://mofluid.com/features/" target="_blank">Click Here</a>',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => $helper->__('Disable'),
              ),

              array(
                  'value'     => 1,
                  'label'     => $helper->__('Enable'),
              ),
          ),
       ));
        $configuration_fieldset->addField('mofluid_theme_display_custom_attribute', 'select', array(
          'label'     => $helper->__('Product Custom Attribute'),
          'name'      => 'mofluid_theme_display_custom_attribute',
          'required'  => true,
          'value'     => $elegant_theme_settings['mofluid_theme_display_custom_attribute'],
          'after_element_html' => '<br>Select "Show" if you want to display all the custom attribute associated with product at product description page. ',
          'values'    => array(
              array(
                  'value'     => 0,
                  'label'     => $helper->__('Hide'),
              ),

              array(
                  'value'     => 1,
                  'label'     => $helper->__('Show'),
              ),
          ),
       ));
       $configuration_fieldset->addField('mofluid_theme_custom_footer', 'editor', array(
	       'label' => $helper->__('Custom Footer'),
	       'title' => $helper->__('Custom Footer'),
	       'name' => 'mofluid_theme_custom_footer',
	       'wysiwyg' => true,// enable WYSIWYG editor
	       'after_element_html' => '<br>Leave blank for default footer.',
           'value' => base64_decode($elegant_theme_settings['mofluid_theme_custom_footer']) 
       ));

       
      
       if (Mage::registry('mofluid_thememofluidelegant')) {
            $form->setValues(Mage::registry('mofluid_thememofluidelegant')->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();      
    }

}
