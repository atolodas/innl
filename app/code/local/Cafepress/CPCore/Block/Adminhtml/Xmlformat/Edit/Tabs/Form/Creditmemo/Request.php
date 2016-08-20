<?php

class Cafepress_CPCore_Block_Adminhtml_Xmlformat_Edit_Tabs_Form_Creditmemo_Request extends Cafepress_CPCore_Block_Adminhtml_Xmlformat_Form//Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        
        $form = new Varien_Data_Form();
		$form->setDataObject(Mage::registry('current_xmlformat'));
        $fieldset = $form->addFieldset('general', array('legend'=>Mage::helper('cpcore')->__('Request')));

		$additionalData = array(
			'header'=>array('style'=>'width:500px; height:300px;'),
			'main_part'=>array('style'=>'width:500px; height:300px;'),
			'addresses'=>array('style'=>'width:500px; height:300px;'),
			'product'=>array('style'=>'width:500px; height:300px;'),
			'footer'=>array('style'=>'width:500px; height:300px;'),
			'custom_url'=>array()
			);

		$fieldset->addField('type', 'hidden', array(
            'name'  => 'xmlformat[type]',
            'value' => Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getIdTypeByName('creditmemo')
        ));
		$this->_setFieldset(Mage::getModel('cpcore/xmlformat')->getResource()->loadAllAttributes()->getSortedAttributes(), $fieldset, $additionalData);
        
        if ( Mage::getSingleton('adminhtml/session')->getXmlformatData() )
        {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getXmlformatData());
            Mage::getSingleton('adminhtml/session')->setXmlformatData(null);
        } elseif (Mage::registry('current_xmlformat')) {
            $form->addValues(Mage::registry('current_xmlformat')->getData());
        }

        $this->setForm($form);
    }
    
    
    private function getFormat()
    {
        if (!($this->getData('format') instanceof Cafepress_CPCore_Model_Xmlformat)) {
            $this->setData('format', Mage::registry('current_xmlformat'));
        }
        return $this->getData('format');
    }
    
}
