<?php 

/**
 * Limit product fields admin controller
 *
 * @category   BusinessKing
 * @package    BusinessKing_ProductFieldsPermission
 */
class BusinessKing_ProductFieldsPermission_Adminhtml_Product_FieldsController extends Mage_Adminhtml_Controller_Action
{
	public function _initAction()
    {
        $this->loadLayout()
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Product Fields'), Mage::helper('adminhtml')->__('Manage Product Fields'));           
        return $this;
    }
    
	public function indexAction()
	{
		$this->_initAction()
            ->_setActiveMenu('product/fields')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Product Fields Permission'), Mage::helper('adminhtml')->__('Manage Product Fields Permission'))
            ->_addContent($this->getLayout()->createBlock('productfieldspermission/adminhtml_product_fields'))
            ->renderLayout();
	}
	
	public function saveAction()
	{
		$role = $this->getRequest()->getPost('role');
		$attributes = $this->getRequest()->getPost('attribute');
		Mage::getModel('productfieldspermission/product_fields')->removeReadOnlyFields($role);
		if (count($attributes) > 0) {
			foreach ($attributes as $attribute) {
				$data = array(
					'role_id' => $role,
					'attribute_id' => $attribute,
					'tab_name' => ''					
				);
				Mage::getModel('productfieldspermission/product_fields')->setReadOnlyFields($data);
			}
		}
		$inventoryTab = $this->getRequest()->getPost('inventory_tab');
		if ($inventoryTab) {
			$data = array(
				'role_id' => $role,
				'attribute_id' => 0,
				'tab_name' => 'inventory_tab'					
			);
			Mage::getModel('productfieldspermission/product_fields')->setReadOnlyFields($data);
		}
		$websitesTab = $this->getRequest()->getPost('websites_tab');
		if ($websitesTab) {
			$data = array(
				'role_id' => $role,
				'attribute_id' => 0,
				'tab_name' => 'websites_tab'					
			);
			Mage::getModel('productfieldspermission/product_fields')->setReadOnlyFields($data);
		}
		
		Mage::getSingleton('checkout/session')->setData('productFields', Mage::helper('adminhtml')->__('Product fields permission saved successfully.'));
		$this->_redirect('*/*/index');
	}
	
	protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('product/fields');
    }
}