<?php
/**
 * Ultimate_Events extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Ultimate
 * @package        Ultimate_Events
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Events comment edit form tab
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Block_Adminhtml_Events_Comment_Edit_Tab_Form
    extends Mage_Adminhtml_Block_Widget_Form {
    /**
     * prepare the form
     * @access protected
     * @return Events_Events_Block_Adminhtml_Events_Comment_Edit_Tab_Form
     * @author Ultimate Module Creator
     */
    protected function _prepareForm(){
        $events = Mage::registry('current_events');
        $comment    = Mage::registry('current_comment');
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('comment_');
        $form->setFieldNameSuffix('comment');
        $this->setForm($form);
        $fieldset = $form->addFieldset('comment_form', array('legend'=>Mage::helper('ultimate_events')->__('Comment')));
        $fieldset->addField('events_id', 'hidden', array(
            'name'  => 'events_id',
            'after_element_html' => '<a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/events_events/edit', array('id'=>$events->getId())).'" target="_blank">'.Mage::helper('ultimate_events')->__('Events').' : '.$events->getTitle().'</a>'
        ));
        $fieldset->addField('title', 'text', array(
            'label' => Mage::helper('ultimate_events')->__('Title'),
            'name'  => 'title',
            'required'  => true,
            'class' => 'required-entry',
        ));
        $fieldset->addField('comment', 'textarea', array(
            'label' => Mage::helper('ultimate_events')->__('Comment'),
            'name'  => 'comment',
            'required'  => true,
            'class' => 'required-entry',
        ));
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('ultimate_events')->__('Status'),
            'name'  => 'status',
            'required'  => true,
            'class' => 'required-entry',
            'values'=> array(
                array(
                    'value' => Ultimate_Events_Model_Events_Comment::STATUS_PENDING,
                    'label' => Mage::helper('ultimate_events')->__('Pending'),
                ),
                array(
                    'value' => Ultimate_Events_Model_Events_Comment::STATUS_APPROVED,
                    'label' => Mage::helper('ultimate_events')->__('Approved'),
                ),
                array(
                    'value' => Ultimate_Events_Model_Events_Comment::STATUS_REJECTED,
                    'label' => Mage::helper('ultimate_events')->__('Rejected'),
                ),
            ),
        ));
        $configuration = array(
             'label' => Mage::helper('ultimate_events')->__('Poster name'),
             'name'  => 'name',
             'required'  => true,
             'class' => 'required-entry',
        );
        if ($comment->getCustomerId()) {
            $configuration['after_element_html'] = '<a href="'.Mage::helper('adminhtml')->getUrl('adminhtml/customer/edit', array('id'=>$comment->getCustomerId())).'" target="_blank">'.Mage::helper('ultimate_events')->__('Customer profile').'</a>';
        }
        $fieldset->addField('name', 'text', $configuration);
        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('ultimate_events')->__('Poster e-mail'),
            'name'  => 'email',
            'required'  => true,
            'class' => 'required-entry',
        ));
        $fieldset->addField('customer_id', 'hidden', array(
            'name'  => 'customer_id',
        ));

        if (Mage::app()->isSingleStoreMode()){
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            Mage::registry('current_comment')->setStoreId(Mage::app()->getStore(true)->getId());
        }
        $form->addValues($this->getComment()->getData());
        return parent::_prepareForm();
    }
    /**
     * get the current comment
     * @access public
     * @return Ultimate_Events_Model_Events_Comment
     */
    public function getComment(){
        return Mage::registry('current_comment');
    }
}