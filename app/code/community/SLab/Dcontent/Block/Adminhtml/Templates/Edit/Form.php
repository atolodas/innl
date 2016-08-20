<?php
/**
 * Template edit page form
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Templates_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  /**
   * Prepare form container
   *
   * @return this
   */
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );
 	
      $form->setUseContainer(true);
      $this->setForm($form);
      return parent::_prepareForm();
  }
}