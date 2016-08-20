<?php

class Cafepress_CPWms_Block_Catalog_Product_Create_Tab_Createremote extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    protected function _prepareForm()
    {
        $data = $this->getRequest()->getParams();
        $merchandiseData = Mage::getModel('cpwms/cafepress_product')->createRemoteProduct($data);

        $form = new Varien_Data_Form(array(
            'id' => 'token_form',
            'action' => $this->getUrl('*/*/continue', array(
                'id'    => $this->getRequest()->getParam('id'),
                'token' => Mage::getModel('cpwms/cafepress_token')->get(),
                'action'=> 'image'
            )),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));


        $fieldset = $form->addFieldset('setprint', array('legend'=>Mage::helper('cpwms')->__('Select Product Type')));

        $fieldset->addField('xml_data', 'textarea', array(
            'label'     => $this->__('XML Data'),
            'name'      => 'xml_data',
            'style'     => 'width:200%;',
            'value'     => $merchandiseData
        ));

        $this->setForm($form);
    }

    public function getContinueUrl()
    {
        return $this->getUrl('*/*/continue', array(
            '_current'      => true,
            'id'            => $this->getRequest()->getParam('id'),
            'token'         => $this->getRequest()->getParam('token'),
            'action'        => 'selectmerchant'
        ));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/continue', array(
            '_current'      => true,
            'id'            => $this->getRequest()->getParam('id'),
            'token'        => $this->getRequest()->getParam('token'),
            'action'        => 'savetoken'
        ));
    }
}
