<?php

class Cafepress_CPWms_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Settings extends Cafepress_CPWms_Block_Adminhtml_Xmlformat_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('continue_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('cpwms')->__('Continue'),
                    'onclick'   => "setSettings('".$this->getContinueUrl()."','format_type')",
                    'class'     => 'save'
                    ))
                );
        parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Settings
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('settings', array('legend'=>Mage::helper('cpwms')->__('Type Xml Format')));
       
        $typeSource = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getOptions();
        $fieldset->addField('format_type', 'select', array(
                'name'      => 'xmlformat[type]',
                'label'     => Mage::helper('cpwms')->__('Type'),
                'required'  => true,
                'values'    => $typeSource,
        ));
        
        $fieldset->addField('continue_button', 'note', array(
            'text' => $this->getChildHtml('continue_button'),
        ));


        $this->setForm($form);
    }

    /**
     * Retrieve Continue URL
     *
     * @return string
     */
    public function getContinueUrl()
    {
        return $this->getUrl('*/*/edit', array(
            '_current'  => true,
            'type'      => '{{type}}'
        ));
    }

    /**
     * Retrieve Back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/new', array('set'=>null, 'type'=>null));
    }
}
