<?php

class Cafepress_CPWms_Block_Adminhtml_Replacer_Edit_Tabs_Form_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        $this->setChild('add_value_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label'     => Mage::helper('cpwms')->__('Add Value'),
                'onclick'   => "addValue('".$this->getNewLineContent()."'); return false;",
                'class'     => 'add add-value-button',
            ))
        );
        
        $this->setChild('get_possible_values_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                'label'     => Mage::helper('cpwms')->__('Get Possible Values'),
                'onclick'   => "getPossibleValues(); return false;",
                'class'     => 'save',
            ))
        );

        $this->setChild('dynamic_table',
            $this->getLayout()->createBlock('cpwms/adminhtml_replacer_edit_dynamictable'));


        parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_replacer'));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'    =>  Mage::helper('cpwms')->__('Replacer')
        ));

//        $fieldset->addField('pattern', 'text', array(
//            'label'     => Mage::helper('cpwms')->__('Construction'),
//            'name'      => 'replacer[pattern]',
//            'required'  => true,
//            'style'     => 'width:100%;',
//            'note'      => Mage::helper('cpwms')->__('Example:{{var order.getCreatedAt()}}'),
//        ));
        
        $fieldset->addField('helper', 'text', array(
            'label'     => Mage::helper('cpwms')->__('Helper'),
            'name'      => 'replacer[helper]',
            'required'  => true,
            'style'     => 'width:100%;',
            'note'      => Mage::helper('cpwms')->__('Example:"test_helper" from "{{var order.getShippingMethod()|test_helper}}"'),
        ));
        $fieldset->addField('conditions', 'text', array(
            'label'     => Mage::helper('cpwms')->__('Condition'),
            'name'      => 'replacer[conditions]',
//            'required'  => true,
            'style'     => 'width:100%;',
            'note'      => Mage::helper('cpwms')->__('Example: {{cond ![[var order.isWmsRequestStatus(last,Sent:Created)]]}}'),
        ));

        $fieldset->addField('add_value_button', 'note', array(
            'text' => $this->getChildHtml('add_value_button'),
        ));

        $fieldset->addField('dynamic_table', 'note', array(
            'text' => $this->getChildHtml('dynamic_table'),
        ));
        
        
        $fieldsetPossible = $form->addFieldset('orders_fieldset', array('legend'=>Mage::helper('cpwms')->__('Possible Values')));

        $fieldsetPossible->addField('possible_condition', 'text', array(
            'label'     => Mage::helper('cpwms')->__('Possible Construction'),
            'name'      => 'possible_condition',
            'style'     => 'width:100%;',
            'note'      => Mage::helper('cpwms')->__('{{var order.getShippingMethod()|shipping_method}}'),
        ));
        
        $fieldsetPossible->addField('get_possible_values_button', 'note', array(
            'text' => $this->getChildHtml('get_possible_values_button'),
        ));
        
        $fieldsetPossible->addField('possible_values_grid', 'note', array(
            'text' => '<div id="possible_values_grid_block"></div>',
        ));

        if ( Mage::getSingleton('adminhtml/session')->getReplacerData() ) {
            $values = Mage::getSingleton('adminhtml/session')->getReplacerData();

            $form->setValues($values);
            Mage::getSingleton('adminhtml/session')->setReplacerData(null);

        } elseif ( Mage::registry('current_replacer') ) {
            $values = Mage::registry('current_replacer')->getData();
            $form->setValues($values);
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }


    public function getReplacer()
    {
        return Mage::registry('current_replacer');
    }

    public function getNewLineContent(){
        $newLineContent = $this->getLayout()->createBlock('cpwms/adminhtml_replacer_edit_dynamictable_newline')->toHtml();
        $result = base64_encode($newLineContent);

        return $result;
    }


}