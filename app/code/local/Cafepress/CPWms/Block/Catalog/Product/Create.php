<?php

class Cafepress_CPWms_Block_Catalog_Product_Create extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cpwms/cofepress/product/create.phtml');
        $this->setId('product_create');
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('catalog')->__('Cancel'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                    'class' => 'back'
                ))
        );

        switch($this->getRequest()->getParam('action')){
            case '':
                $this->setChild('continue_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                        'label' => Mage::helper('catalog')->__('Set Print'),
                        'onclick' => 'productForm.submit()',
                        'class' => 'save'
                    ))
                );
                break;
            case 'setprint':
                $this->setChild('continue_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                        'label' => Mage::helper('catalog')->__('Set Print'),
                        'onclick' => 'productForm.submit()',
                        'class' => 'save'
                    ))
                );
                break;
            case 'merchandise':
                $this->setChild('continue_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                        'label' => Mage::helper('catalog')->__('Set Product Type'),
                        'onclick' => 'productForm.submit()',
                        'class' => 'save'
                    ))
                );
                break;
            case 'setparams':
                $this->setChild('continue_button',
                    $this->getLayout()->createBlock('adminhtml/widget_button')
                        ->setData(array(
                        'label' => Mage::helper('catalog')->__('Set Product Params'),
                        'onclick' => 'productForm.submit()',
                        'class' => 'save'
                    ))
                );
                break;
        }

        return parent::_prepareLayout();
    }

    public function getCreateTokenUrl()
    {
        return $this->getUrl('*/*/createToken',array(
            'id' => $this->getRequest()->getParam('id')
        ));
    }

    public function getContinueButtonHtml()
    {
        return $this->getChildHtml('continue_button');
    }
    
    public function getContinueUrl()
    {
        $productId = $this->getRequest()->getParam('id');
        $userToken = $this->getRequest()->getParam('token');
        $activTab = Mage::registry('cpwms_activ_tab');
        return $this->getUrl('*/*/continue', array(
            '_current'      => true,
            'id'        =>  $productId,
            'token'     =>  $userToken,
            'action' => $activTab
        ));
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

    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button');
    }

    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/continue', array('_current'=>true, 'back'=>null));
    }

    public function getSaveAndContinueUrl()
    {
        $activTab = Mage::registry('cpwms_activ_tab');
        return $this->getUrl('*/*/continue', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'action' => $activTab
        ));
    }

    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
    }

    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
    }

    public function getAttributeSetName()
    {
        if ($setId = $this->getProduct()->getAttributeSetId()) {
            $set = Mage::getModel('eav/entity_attribute_set')
                ->load($setId);
            return $set->getAttributeSetName();
        }
        return '';
    }

    public function getIsConfigured()
    {
        if ($this->getProduct()->isConfigurable()
            && !($superAttributes = $this->getProduct()->getTypeInstance(true)->getUsedProductAttributeIds($this->getProduct()))) {
            $superAttributes = false;
        }

        return !$this->getProduct()->isConfigurable() || $superAttributes !== false;
    }

    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }
}
