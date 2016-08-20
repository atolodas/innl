<?php

class Cafepress_CPWms_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Transformer_Response extends Cafepress_CPWms_Block_Adminhtml_Xmlformat_Form
{

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $form->setDataObject(Mage::registry('current_xmlformat'));
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpwms')->__('Response')));

        $ResponseTypeSource = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_Responsemethods')->getAllOptions();
        
        $additionalData = array(
			'response_method'=>array('source'=>$ResponseTypeSource),
			'pattern_response'=>array('style'=>'width:500px;'),
//			'request'=>array('style'=>'width:500px; height:200px;'),
			'response'=>array('style'=>'width:500px; height:300px;'),
//			'status'=>array('source'=>$statusSource),
//			'schedule'=>array('source'=>$scheduleSource)
			);

        $fieldset->addField('type', 'hidden', array(
            'name'  => 'xmlformat[type]',
            'value' => Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('transformer'),
        ));
        $this->_setFieldset(Mage::getModel('cpwms/xmlformat')->getResource()->loadAllAttributes()->getSortedAttributes(), $fieldset, $additionalData);

        if ( Mage::getSingleton('adminhtml/session')->getXmlformatData() )
        {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getXmlformatData());
            Mage::getSingleton('adminhtml/session')->setXmlformatData(null);
        } elseif (Mage::registry('current_xmlformat')) {
            $form->addValues(Mage::registry('current_xmlformat')->getData());
        }

        $this->setForm($form);
    }
    
}
