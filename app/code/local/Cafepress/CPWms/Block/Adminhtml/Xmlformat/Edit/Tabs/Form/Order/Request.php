<?php

class Cafepress_CPWms_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Order_Request extends Cafepress_CPWms_Block_Adminhtml_Xmlformat_Form//Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {

        $form = new Varien_Data_Form();
		$form->setDataObject(Mage::registry('current_xmlformat'));
        $fieldset = $form->addFieldset('request', array('legend'=>Mage::helper('cpwms')->__('Request')));

		$additionalData = array(
			'header'=>array('style'=>'width:500px; height:300px;','id'=>'code'),
			'main_part'=>array('style'=>'width:500px; height:300px;'),
			'addresses'=>array('style'=>'width:500px; height:300px;'),
			'product'=>array('style'=>'width:500px; height:300px;'),
			'footer'=>array('style'=>'width:500px; height:300px;'),
			'custom_url'=>array()
			);
		 $fieldset->addField('type', 'hidden', array(
            'name'  => 'xmlformat[type]',
            'value' => Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('order')
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
