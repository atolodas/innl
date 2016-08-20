<?php
class Neklo_ABTesting_Block_Adminhtml_System_Abtest_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    
    public function __construct() {
        parent::__construct();
        $this->setId('abtest_form');
        $this->setTitle(Mage::helper('neklo_abtesting')->__('A/B Test Information'));
    }
    
    
    protected function _prepareForm() {
        $helper = Mage::helper('neklo_abtesting');
        $model = $this->getAbtest();

        $form = new Varien_Data_Form(
            array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post')
        );

        $form->setHtmlIdPrefix('abtest_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $helper->__('General Information'), 'class' => 'fieldset-wide'));

        if ($model->getAbtestId()) {
            $fieldset->addField('abtest_id', 'hidden', array(
                'name' => 'abtest_id',
            ));
        }

        $fieldset->addField('name', 'text', array(
            'name'      => 'name',
            'label'     => $helper->__('A/B Test Title'),
            'title'     => $helper->__('A/B Test Title'),
            'required'  => true,
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

        $fieldset->addField('code', 'text', array(
            'name'      => 'code',
            'label'     => $helper->__('Code'),
            'title'     => $helper->__('Code'),
            'required'  => true,
            'class'     => 'validate-code',
        ));

        $fieldset->addField('cookie_lifetime', 'text', array(
            'name'      => 'cookie_lifetime',
            'label'     => $helper->__('Cookie Lifetime'),
            'title'     => $helper->__('Cookie Lifetime'),
            'required'  => true,
            'class'     => 'alidate-zero-or-greater',
            'note'      => 'in hours'
        ));

        if (!$model->getId()) {
            $model->setData('cookie_lifetime', '24');
        }

        $fieldset->addField('events', 'multiselect', array(
            'label'     => $helper->__('Success events'),
            'title'     => $helper->__('Success events'),
            'name'      => 'events',
            'required'  => false,
            'values'   => Mage::getModel('neklo_abtesting/system_config_source_event')->toOptionArray(),
        ));

        $model->setData('events', $model->getSuccessEvents()->getColumnValues('event_id'));
      
        $fieldset->addField('presentations', 'text', array(
                'label'     => $helper->__('Presentations'),
                'name'=>'presentations',
                'class'=>'requried-entry',
                'value'=>$model->getPresentations()
        ));

        $form->getElement('presentations')->setRenderer(
            $this->getLayout()->createBlock('neklo_abtesting/adminhtml_neklo_abtesting_presentations')
        );

        $dateFormatIso = Mage::app()->getLocale()->getDateTimeFormat(
            Mage_Core_Model_Locale::FORMAT_TYPE_SHORT
        );

        $fieldset->addField('start_at', 'date', array(
                'label'        => Mage::helper('core')->__('Start Date'),
                'name'         => 'start_at',
                'required'     => false, 
                'time' => true,
                'image'        => $this->getSkinUrl('images/grid-cal.gif'),
                'format'       => $dateFormatIso
        ));

        $fieldset->addField('end_at', 'date', array(
                'label'        => Mage::helper('core')->__('End Date'),
                'name'         => 'end_at', 
                'required' => false,
                'time'         => true,
                'image'        => $this->getSkinUrl('images/grid-cal.gif'),
                'format'       => $dateFormatIso
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    
    
    
    public function getAbtest() {
        return Mage::registry('current_abtest');
    }
    
    public function getFormatedDate($date) {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, true);
    }
}
