<?php
class DP_Social_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/popup?id=15 
    	 *  or
    	 * http://site.com/popup/id/15 	
    	 */
    	/* 
		$popup_id = $this->getRequest()->getParam('id');

  		if($popup_id != null && $popup_id != '')	{
			$popup = Mage::getModel('popup/popup')->load($popup_id)->getData();
		} else {
			$popup = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($popup == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$popupTable = $resource->getTableName('popup');
			
			$select = $read->select()
			   ->from($popupTable,array('popup_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$popup = $read->fetchRow($select);
		}
		Mage::register('popup', $popup);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}