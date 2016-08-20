<?php
/**
 * Blocks edit page form
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Templates_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form

{

  protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }
  /**
   * Prepare  form
   *
   * @return this
   */
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('dcontent_form', array('legend'=>Mage::helper('dcontent')->__('Template information')));

      $fieldset->addField('header', 'textarea', array(
          'label'     => Mage::helper('dcontent')->__('Title'),
          'required'  => false,
          'name'      => 'header',
      	 'note'		=> Mage::helper('dcontent')->__('just for admin')
      ));

      $field = $fieldset->addField('store_id', 'multiselect', array(
          'name'      => 'store_id',
          'label'     => Mage::helper('cms')->__('Store View'),
          'title'     => Mage::helper('cms')->__('Store View'),
          'required'  => true,
          'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
          'disabled'  => false,
      ));
      $renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
      $field->setRenderer($renderer);

      $fieldset->addField('before_products', 'textarea', array(
          'label'     => Mage::helper('dcontent')->__('Before all products html'),
          'required'  => false,
          'name'      => 'before_products',
      	  'style'     => 'height:36em;width:200%',
         'note'		=> 'Any html allowed.'
      ));

      $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig();

      $fieldset->addField('product', 'editor', array(
          'label'     => Mage::helper('dcontent')->__('Product Template'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'product',
          'config'    => $wysiwygConfig,
      	  'style'     => 'height:36em;width:200%',
         'note'		=> Mage::helper('dcontent')->__('Any html allowed. You can use {{ANY_PRODUCT_ATTRIBUTE_CODE}} construction here (ex. \'{{sku}}\'). It will be replaced by attribute of each product. Also you can use formating in next way {{sku.format(bold)}} or {{price.format(price)}} or {{description.format(upercase bold italic)}}. List of available formats: \'price\',\'image\',\'upercase\',\'lowercase\',\'capitalize\',\'bold\',\'italic\',\'strike\',\'underline\'.')
      ));

         $contentField2 = $fieldset->addField('col_left', 'editor', array(
            'name'      => 'col_left',
            'label'    => Mage::helper('cms')->__('Left column'),

          'style'     => 'height:36em;width:200%',
              'config'    => $wysiwygConfig,

        ));

          $contentField3 = $fieldset->addField('col_right', 'editor', array(
            'name'      => 'col_right',
            'label'    => Mage::helper('cms')->__('Right column'),
           'style'     => 'height:36em;width:200%',
             'config'    => $wysiwygConfig,

        ));

$fieldset->addField('category', 'textarea', array(
          'label'     => Mage::helper('dcontent')->__('Category template'),
          'required'  => false,
          'name'      => 'category',
          'style'     => 'height:36em;width:200%',
          'note'                => Mage::helper('dcontent')->__('Any html allowed')
      ));

$fieldset->addField('additional_data', 'textarea', array(
          'label'     => Mage::helper('dcontent')->__('Additional data'),
          'required'  => false,
          'name'      => 'additional_data',
          'style'     => 'height:36em;width:200%',
          'note'                => Mage::helper('dcontent')->__('Any html allowed')
      ));

      $fieldset->addField('after_products', 'textarea', array(
          'label'     => Mage::helper('dcontent')->__('After all products html'),
          'required'  => false,
          'name'      => 'after_products',
      	  'style'     => 'height:36em;width:200%',
          'note'		=> Mage::helper('dcontent')->__('Any html allowed')
      ));

      $fieldset->addField('separator', 'textarea', array(
          'label'     => Mage::helper('dcontent')->__('Separator (will be placed between products)'),
          'required'  => false,
          'name'      => 'separator',
      	'note'	=> Mage::helper('dcontent')->__('Any html allowed')
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
