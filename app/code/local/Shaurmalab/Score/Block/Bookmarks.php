<?php

class Shaurmalab_Score_Block_Bookmarks extends Mage_Wishlist_Block_Customer_Wishlist
{
  /**
     * Default toolbar block name
     *
     * @var string
     */
    protected $_defaultToolbarBlock = 'score/oggetto_list_toolbar';
/**
     * Get score layer model
     *
     * @return Shaurmalab_Score_Model_Layer
     */
    public function getLayer()
    {
        $layer = Mage::registry('current_layer');
        if ($layer) {
            return $layer;
        }
        return Mage::getSingleton('score/layer');
    }
   /**
     * Need use as _prepareLayout - but problem in declaring collection from
     * another block (was problem with search result)
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getWishlistItems();
    
        $toolbar->setAvailableOrders(array('wishlist_item_id'));
         $pager = $this->getLayout()->createBlock('page/html_pager')->setTemplate('page/html/scorepager.phtml');
         $pager->setCollection($collection);
        $this->setChild('oggetto_list_toolbar_pager',$pager);
        
        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        Mage::dispatchEvent('score_block_oggetto_list_collection', array(
            'collection' => $collection
        ));

        $this->getWishlistItems()->load();

        return parent::_beforeToHtml();
    }

     /**
     * Retrieve Toolbar block
     *
     * @return Shaurmalab_Score_Block_Oggetto_List_Toolbar
     */
    public function getToolbarBlock()
    {
        if ($blockName = $this->getToolbarBlockName()) {
            if ($block = $this->getLayout()->getBlock($blockName)) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
        return $block;
    }

     /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
 
   /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('oggetto_list_toolbar_pager');
    }
	/**
     * Retrieve Wishlist Product Items collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    public function getWishlistItems()
    {
        if (is_null($this->_collection)) {
            $this->_collection = $this->_createWishlistItemCollection();
            $this->_collection->addFieldToFilter('oggetto_id',array('gt'=>0));
            $this->_collection->setOrder('wishlist_item_id','desc');
            $this->_prepareCollection($this->_collection);
        }

        return $this->_collection;
    }

    public function getWishlistIds() { 
		if(!Mage::registry('wishlist_ids')) { 
			$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();

			if(!$customerId) return array();

			$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customerId, true);
			
			$ids = $wishlist->getItemCollection()->getColumnValues('oggetto_id');
			
			Mage::register('wishlist_ids',serialize($ids));
		}
    	
    	return unserialize(Mage::registry('wishlist_ids'));
    }
}
