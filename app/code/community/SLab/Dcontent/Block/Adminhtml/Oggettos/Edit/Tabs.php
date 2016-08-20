<?php
/**
 * Block edit page tabs
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Oggettos_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
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
      $this->setTitle(Mage::helper('dcontent')->__('Edit block'));
  }
  
  	/**
	 * Add tabs
	 *
	 * @return this
	 */
	protected function _beforeToHtml()
	{
		$this->addTab('form_section', array(
			'label'     => Mage::helper('dcontent')->__('Block Main Information'),
			'title'     => Mage::helper('dcontent')->__('Block Main Information'),
			'content'   => $this->getLayout()->createBlock('dcontent/adminhtml_dcontent_edit_tab_form')->toHtml(),
		));
      
       $this->addTab('oggettos', array(
                'label'     => Mage::helper('dcontent')->__('Oggettos'),
				'class'     => 'ajax',
       			'url'       => $this->getUrl('*/*/products', array('_current' => true)),
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