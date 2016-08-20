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
 * Admin scoretag edit block
 *
 * @deprecated after 1.3.2.3
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Scoretag_Scoretag_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId = 'scoretag_id';
        $this->_controller = 'scoretag';

        parent::__construct();

        if( $this->getRequest()->getParam('oggetto_id') ) {
            $this->_updateButton('back', 'onclick', "setLocation('" . $this->getUrl('*/score_oggetto/edit', array('id' => $this->getRequest()->getParam('oggetto_id'))) . "')");
        }

        if( $this->getRequest()->getParam('customer_id') ) {
            $this->_updateButton('back', 'onclick', "setLocation('" . $this->getUrl('*/customer/edit', array('id' => $this->getRequest()->getParam('customer_id'))) . "')");
        }

        if( $this->getRequest()->getParam('ret', false) == 'pending' ) {
            $this->_updateButton('back', 'onclick', 'setLocation(\'' . $this->getUrl('*/*/pending') .'\')' );
            $this->_updateButton('delete', 'onclick', 'deleteConfirm(\'' . Mage::helper('scoretag')->__('Are you sure you want to do this?') . '\', \'' . $this->getUrl('*/*/delete', array(
                $this->_objectId => $this->getRequest()->getParam($this->_objectId),
                'ret'           => 'pending',
            )) .'\')' );
            Mage::register('ret', 'pending');
        }

        $this->_updateButton('save', 'label', Mage::helper('scoretag')->__('Save Scoretag'));
        $this->_updateButton('delete', 'label', Mage::helper('scoretag')->__('Delete Scoretag'));
    }

    /**
     * Add to layout accordion block
     *
     * @return Mage_Adminhtml_Block_Scoretag_Edit
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setChild('accordion', $this->getLayout()->createBlock('adminhtml/scoretag_edit_accordion'));
        return $this;
    }

    /**
     * Adds to html of form html of accordion block
     *
     * @return string
     */
    public function getFormHtml()
    {
        $html = parent::getFormHtml();
        return $html . $this->getChildHtml('accordion');
    }

    public function getHeaderText()
    {
        if (Mage::registry('scoretag_scoretag')->getId()) {
            return Mage::helper('scoretag')->__("Edit Scoretag '%s'", $this->escapeHtml(Mage::registry('scoretag_scoretag')->getName()));
        }
        else {
            return Mage::helper('scoretag')->__('New Scoretag');
        }
    }

}
