
<?php

class Shaurmalab_Score_BookmarksController extends Mage_Core_Controller_Front_Action
{
    /**
    * Index action
    */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_initLayoutMessages('customer/session');
        $this->_initLayoutMessages('catalog/session');
        $this->getLayout()->getBlock('head')->setTitle(Mage::helper('customer')->__('Bookmarks'));
        $this->renderLayout();
    }

       /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
}