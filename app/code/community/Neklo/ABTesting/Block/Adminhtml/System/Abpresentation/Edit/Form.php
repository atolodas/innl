<?php
class Neklo_ABTesting_Block_Adminhtml_System_Abpresentation_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    
    public function __construct() {
        parent::__construct();
        $this->setId('abtest_form');
        $this->setTitle(Mage::helper('neklo_abtesting')->__('A/B Presentation Information'));
    }
    
    
    protected function _prepareForm() {
        $helper = Mage::helper('neklo_abtesting');
        $model = $this->getAbPresentation();

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('abtest_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getPresentationId()) {
            $fieldset->addField('presentation_id', 'hidden', array(
                'name' => 'presentation_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => $helper->__('A/B Presentation Title'),
            'title'     => $helper->__('A/B Presentation Title'),
            'required'  => true,
        ));

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => $helper->__('Code'),
            'title'     => $helper->__('Code'),
            'required'  => true,
            'class'     => 'validate-code',
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('checkout')->__('Status'),
            'title'     => Mage::helper('checkout')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'options'   => array(
                '1' => Mage::helper('checkout')->__('Enabled'),
                '0' => Mage::helper('checkout')->__('Disabled'),
            ),
        ));

        $fieldset->addField('html_content', 'textarea', array(
            'label' => $this->helper('core')->__('HTML Content'),
            'name' => 'html_content'
        ));

        $fieldset->addField('layout_update', 'textarea', array(
            'label' => $this->helper('core')->__('Layout Update'),
            'name' => 'layout_update'
        ));

        
        $form->setValues($model->getData());
        if ($data =  Mage::getSingleton('adminhtml/session')->getFormData()) {
            $form->setValues($data);
        }
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    
    
    public function getAbpresentation() {
        return Mage::registry('current_presentation');
    }
    
    public function getFormatedDate($date) {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, true);
    }
}
