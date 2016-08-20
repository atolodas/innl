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
 * Adminhtml scoretag edit form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Scoretag_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('scoretag_form');
        $this->setTitle(Mage::helper('scoretag')->__('Block Information'));
    }

    /**
     * Prepare form
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $model = Mage::registry('scoretag_scoretag');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $fieldset = $form->addFieldset('base_fieldset',
            array('legend'=>Mage::helper('scoretag')->__('General Information')));

        if ($model->getScoretagId()) {
            $fieldset->addField('scoretag_id', 'hidden', array(
                'name' => 'scoretag_id',
            ));
        }

        $fieldset->addField('form_key', 'hidden', array(
            'name'  => 'form_key',
            'value' => Mage::getSingleton('core/session')->getFormKey(),
        ));

        $fieldset->addField('store_id', 'hidden', array(
            'name'  => 'store_id',
            'value' => (int)$this->getRequest()->getParam('store')
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'scoretag_name',
            'label' => Mage::helper('scoretag')->__('Scoretag Name'),
            'title' => Mage::helper('scoretag')->__('Scoretag Name'),
            'required' => true,
            'after_element_html' => ' ' . Mage::helper('adminhtml')->__('[GLOBAL]'),
        ));

        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('scoretag')->__('Status'),
            'title' => Mage::helper('scoretag')->__('Status'),
            'name' => 'scoretag_status',
            'required' => true,
            'options' => array(
                Mage_Scoretag_Model_Scoretag::STATUS_DISABLED => Mage::helper('scoretag')->__('Disabled'),
                Mage_Scoretag_Model_Scoretag::STATUS_PENDING  => Mage::helper('scoretag')->__('Pending'),
                Mage_Scoretag_Model_Scoretag::STATUS_APPROVED => Mage::helper('scoretag')->__('Approved'),
            ),
            'after_element_html' => ' ' . Mage::helper('adminhtml')->__('[GLOBAL]'),
        ));

        $fieldset->addField('base_popularity', 'text', array(
            'name' => 'base_popularity',
            'label' => Mage::helper('scoretag')->__('Base Popularity'),
            'title' => Mage::helper('scoretag')->__('Base Popularity'),
            'after_element_html' => ' ' . Mage::helper('scoretag')->__('[STORE VIEW]'),
        ));

        if (!$model->getId() && !Mage::getSingleton('adminhtml/session')->getScoretagData() ) {
            $model->setStatus(Mage_Scoretag_Model_Scoretag::STATUS_APPROVED);
        }

        if ( Mage::getSingleton('adminhtml/session')->getScoretagData() ) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getScoretagData());
            Mage::getSingleton('adminhtml/session')->setScoretagData(null);
        } else {
            $form->addValues($model->getData());
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }
}
