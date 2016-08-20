<?php
/**
 * Blocks edit page form
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Templates_Editoggettos_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
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
      $fieldset = $form->addFieldset('dcontent_form', array('legend'=>Mage::helper('dcontent')->__('Template information')));

      $fieldset->addField('header', 'text', array(
          'label'     => Mage::helper('dcontent')->__('Title'),
          'required'  => false,
          'name'      => 'header',
      	 'note'		=> Mage::helper('dcontent')->__('just for admin')
      ));

    $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

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

	  $fieldset->addField('type', 'select', array(
          'label'     => Mage::helper('dcontent')->__('Use for Oggetto Type'),
          'name'      => 'type',
          'values'   => $sets
      ));

      $groups = Mage::getModel('customer/group')->getCollection();

      $kinds = array(
          array('value'=> 'main','label'=>'main'),
          array('value'=> 'parent','label'=>'parent'),
          array('value'=> 'child','label'=>'child'),
          array('value'=> 'list','label'=>'list'),
          array('value'=> 'grid','label'=>'grid'),
          array('value'=> 'mygrid','label'=>'my oggetos grid'),
          array('value'=> 'mylist','label'=>'my oggetos list'),
          array('value'=> 'customer_list','label'=>'customer_list'),
          array('value'=> 'customer_main','label'=>'customer_main'),
      );
      foreach($groups as $group) {
          if($group->getId()==0) continue;
          $fcode = strtolower(str_replace(' ','_',$group->getCode()));
          $kinds[] =  array('value'=> $group->getId().'_customer_main','label'=>$fcode.'_main');

      }

          $fieldset->addField('kind', 'multiselect', array(
          'label'     => Mage::helper('dcontent')->__('Use for Oggetto Type'),
          'name'      => 'kind',
          'values'   => $kinds
      ));

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => $this->getTabId())
        );
      /* $fieldset->addField('before_products', 'textarea', array(
          'label'     => Mage::helper('dcontent')->__('Before all products html'),
          'required'  => false,
          'name'      => 'before_products',
      	  'style'     => 'height:36em;width:200%',
         'note'		=> 'Any html allowed.'
      )); */
      // TODO: Fix editor. codemirror ?
      $fieldset->addField('product', 'editor', array(
          'label'     => Mage::helper('dcontent')->__('Product Template'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'product',
             'config'    => $wysiwygConfig,
      	  'style'     => 'height:36em;width:200%',
         'note'		=> Mage::helper('dcontent')->__('Any html allowed. You can use {{ANY_PRODUCT_ATTRIBUTE_CODE}} construction here (ex. \'{{sku}}\'). It will be replaced by attribute of each product. Also you can use formating in next way {{sku.format(bold)}} or {{price.format(price)}} or {{description.format(upercase bold italic)}}. List of available formats: \'price\',\'image\',\'upercase\',\'lowercase\',\'capitalize\',\'bold\',\'italic\',\'strike\',\'underline\'.')
      ));


/*      $fieldset->addField('after_products', 'textarea', array(
          'label'     => Mage::helper('dcontent')->__('After all products html'),
          'required'  => false,
          'name'      => 'after_products',
      	  'style'     => 'height:36em;width:200%',
          'note'		=> Mage::helper('dcontent')->__('Any html allowed')
      ));
 */
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
