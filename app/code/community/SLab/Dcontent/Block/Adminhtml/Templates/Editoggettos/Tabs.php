<?php
/**
 * Template edit page tabs
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Templates_Editoggettos_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
  /**
   * Prepare page
   *
   */
  public function __construct()
  {
      parent::__construct();
      $this->setId('dcontent_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('dcontent')->__('Edit Template'));
  }
  
	/**
	 * Add main tab
	 *
	 * @return this
	 */
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'     => Mage::helper('dcontent')->__('Main Oggetto Template Info'),
			'title'     => Mage::helper('dcontent')->__('Main Oggetto Template Info'),
			'content'   => $this->getLayout()->createBlock('dcontent/adminhtml_templates_editoggettos_tab_form')->toHtml(),
		));
      
      $this->_updateActiveTab();
      Varien_Profiler::stop('customer/tabs');
       return parent::_beforeToHtml();
  }
  
	/**
	 * Set active tab
	 *
	 */
	protected function _updateActiveTab()
    {
    	$tabId = $this->getRequest()->getParam('tab');
    	
    	if( $tabId ) {
    		
    		$tabId = preg_replace("#{$this->getId()}_#", '', $tabId);
    		if($tabId) {
    			
    			$this->setActiveTab($tabId);
    		}
    	}
    }
}