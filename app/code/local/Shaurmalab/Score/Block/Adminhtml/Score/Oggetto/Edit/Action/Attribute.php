<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml score entity action attribute update
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Edit_Action_Attribute extends Mage_Adminhtml_Block_Widget
{

    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('score')->__('Back'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/score_oggetto/', array('store'=>$this->getRequest()->getParam('store', 0))).'\')',
                    'class' => 'back'
                ))
        );

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('score')->__('Reset'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
                ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('score')->__('Save'),
                    'onclick'   => 'attributesForm.submit()',
                    'class'     => 'save'
                ))
        );
    }

    /**
     * Retrieve selected entitys for update
     *
     * @return unknown
     */
    public function getOggettos()
    {
        return $this->_getHelper()->getOggettos();
    }

    /**
     * Retrieve block attributes update helper
     *
     * @return Mage_Adminhtml_Helper_Catalog_Oggetto_Edit_Action_Attribute
     */
    protected function _getHelper()
    {
        return $this->helper('score/adminhtml_score_oggetto_edit_action_attribute');
    }
    
    /**
     * Retrieve back button html code
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve cancel button html code
     *
     * @return string
     */
     public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    /**
     * Retrieve save button html code
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    /**
     * Get save url
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('store'=>Mage::helper('score/adminhtml_score_oggetto_edit_action_attribute')->getSelectedStoreId()));
    }
    
    /**
     * Get validation url
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }
}
