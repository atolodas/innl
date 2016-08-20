<?php
/**
 * Block edit page form
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Dcontent_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  /**
   * Prepare  form
   *
   * @return this
   */
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('dcontent_form', array('legend'=>Mage::helper('dcontent')->__('Block Main Information')));
     
      $fieldset->addField('title', 'textarea', array(
          'label'     => Mage::helper('dcontent')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      	  'note'	=> Mage::helper('dcontent')->__('just for admin')
      ));
$data = Mage::registry('dcontent_data')->getData();
      $fieldset->addField('image', 'file', array(
          'label'     => Mage::helper('dcontent')->__('Promo label'),
          'class'     => '',
          'required'  => false,
          'name'      => 'image',
          'note'    => "Allowed formats: 'jpg','jpeg','gif','png'",
          'after_element_html' => (($data['image'])?"<img style='width:100px; margin:5px;' src='".Mage::getBaseUrl('media'). DS.'dcontent'.DS.$data['image']."' />":'')
      ));
      
       $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('dcontent')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('dcontent')->__('Enabled'),
              ),
              array(
                  'value'     => 2,
                  'label'     => Mage::helper('dcontent')->__('Disabled'),
              ),
          ),
      ));
      
      $fieldset->addField('products_per_line', 'text', array(
          'label'     => Mage::helper('dcontent')->__('Products per line'),
          'name'      => 'products_per_line',
          'class'     => 'required-entry',
          'required'  => true,
      ));
     
           
      if ( Mage::getSingleton('adminhtml/session')->getDcontentData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getDcontentData());
          Mage::getSingleton('adminhtml/session')->setDcontentData(null);
      } elseif ( Mage::registry('dcontent_data') ) {
          $form->setValues(Mage::registry('dcontent_data')->getData());
      }
      return parent::_prepareForm();
  }
}