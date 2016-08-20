<?php

class Cafepress_CPWms_Block_Adminhtml_Xmlformat_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cpwms/xmlformat/edit.phtml');
        $this->setId('xmlformat_edit');
    }

    /**
     * Retrieve currently edited product object
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getXmlformat()
    {
        return Mage::registry('current_xmlformat');
    }

    protected function _prepareLayout()
    {
        if (!$this->getRequest()->getParam('popup')) {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Back'),
                        'onclick'   => 'setLocation(\''.$this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                        'class' => 'back'
                    ))
            );
        } else {
            $this->setChild('back_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('catalog')->__('Close Window'),
                        'onclick'   => 'window.close()',
                        'class' => 'cancel'
                    ))
            );
        }

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('cpwms')->__('Reset'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
                ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('cpwms')->__('Save'),
                    'onclick'   => 'xmlformatForm.submit()',
                    'class' => 'save'
                ))
        );
        
        $this->setChild('save_and_edit_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('cpwms')->__('Save and Continue Edit'),
                    'onclick'   => 'saveAndContinueEdit(\''.$this->getSaveAndContinueUrl().'\')',
                    'class' => 'save'
                ))
        );
        
        if ($this->getXmlformat()->isDeleteable()) {
            $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('cpwms')->__('Delete'),
                        'onclick'   => 'confirmSetLocation(\''.Mage::helper('cpwms')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
                        'class'  => 'delete'
                    ))
            );
        }
        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveAndEditButtonHtml()
    {
        return $this->getChildHtml('save_and_edit_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    public function getXmlformatId()
    {
        return $this->getXmlformat()->getId();
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getHeader()
    {
        $formatName = false;
        $currentXmlformat = Mage::registry('current_xmlformat');
        if($currentXmlformat){
            $formatName = $currentXmlformat->getName();
        }
        $typeCode = Mage::registry('xmlformat_type');
        if(!$typeCode){
            $typeCode = Mage::registry('current_xmlformat')->getType();
        }
        $typeName = Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getNameTypeById($typeCode);
        if($formatName){
            $header = Mage::helper('cpwms')->__('Edit').' "'.$formatName.'" (type: '.$typeName.')';
        } else{
            $header = Mage::helper('cpwms')->__('New Xml Format').' (type: '.$typeName.')';
        }
        return $header;
    }

    public function getUpdateOrdersIdUrl()
    {
        return $this->getUrl('cpwms/index/getOrdersId',
            array(
                '_current'   => true,
            ));
    }
}
