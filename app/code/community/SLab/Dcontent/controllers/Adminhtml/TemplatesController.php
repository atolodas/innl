<?php
/**
 * Templates edit page form
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Adminhtml_TemplatesController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Init controller action
	 *
	 * @return this
	 */
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('dcontent/templates')
			->_addBreadcrumb(Mage::helper('dcontent')->__('Templates Manager'), Mage::helper('dcontent')->__('Templates Manager'));
		
		return $this;
	}   
	
 	/**
	 * Show grid
	 *
	 */
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}
	
	/**
	 * Edit template page
	 *
	 */
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('dcontent/templates')->load($id);
		
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
	
			if (!empty($data)) {
				$model->setData($data);
			}				

			Mage::register('dcontent_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('dcontent/templates');

			$this->_addBreadcrumb(Mage::helper('dcontent')->__('Templates Manager'), Mage::helper('adminhtml')->__('Templates Manager'));
		
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('dcontent/adminhtml_templates_edit'))
				->_addLeft($this->getLayout()->createBlock('dcontent/adminhtml_templates_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Template does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	/**
	 * New template page
	 *
	 */
	public function newAction() {
		$this->_forward('edit');
	}
	
	/**
	 * New template page
	 *
	 */
	public function newOggettosAction() {
		$this->_forward('editOggettos');
	}
	
	/**
	 * Edit template page
	 *
	 */
	public function editOggettosAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('dcontent/templates')->load($id);
		
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
	
			if (!empty($data)) {
				$model->setData($data);
			}				

			Mage::register('dcontent_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('dcontent/templates');

			$this->_addBreadcrumb(Mage::helper('dcontent')->__('Oggettos Templates Manager'), 		Mage::helper('adminhtml')->__('Oggettos Templates Manager'));
		
			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('dcontent/adminhtml_templates_editoggettos'))
				->_addLeft($this->getLayout()->createBlock('dcontent/adminhtml_templates_editoggettos_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Template does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
 
	/**
	 * Save template
	 *
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('dcontent/templates');
            if(isset($data['kind'])) $data['kind'] = implode(',',$data['kind']);
            if(isset($data['store_id'])) $data['store_id'] = implode(',',$data['store_id']);

            $model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				if($model->getStatus()=='') { $model->setStatus(1); }
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dcontent')->__('Template was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Unable to find template to save'));
        $this->_redirect('*/*/');
	}

/**
	 * Save template
	 *
	 */
	public function saveOggettosAction() {
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('dcontent/templates');
			$data['kind'] = implode(',',$data['kind']);
			$model->setData($data)->setId($this->getRequest()->getParam('id'));
			
			try {
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				if($model->getStatus()=='') { $model->setStatus(1); }
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dcontent')->__('Template was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/editOggettos', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/editOggettos', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Unable to find template to save'));
        $this->_redirect('*/*/');
	}
 
	/**
	 * Delete template
	 *
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('dcontent/dcontent');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Template was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	
    /**
     * Mass delete templates
     *
     */
    public function massDeleteAction() {
        $dcontentIds = $this->getRequest()->getParam('dcontent');
        if(!is_array($dcontentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Please select template(s)'));
        } else {
            try {
                foreach ($dcontentIds as $dcontentId) {
                    $dcontent = Mage::getModel('dcontent/templates')->load($dcontentId);
                    $dcontent->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('dcontent')->__(
                        'Total of %d template(s) were successfully deleted', count($dcontentIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * Mass update template status
     *
     */
    public function massStatusAction()
    {
        $dcontentIds = $this->getRequest()->getParam('dcontent');
        if(!is_array($dcontentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Please select template(s)'));
        } else {
            try {
                foreach ($dcontentIds as $dcontentId) {
                    $dcontent = Mage::getSingleton('dcontent/templates')
                        ->load($dcontentId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('dcontent')->__('Total of %d template(s) were successfully updated', count($dcontentIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * Create serializer block for a grid
     *
     * @param string $inputName
     * @param Mage_Adminhtml_Block_Widget_Grid $gridBlock
     * @param array $productsArray
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer
     */
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray)
    {
    	return $this->getLayout()->createBlock('dcontent/adminhtml_dcontent_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setProducts($productsArray)
            ->setInputElementName($inputName);
    }
    
	/**
     * Output specified blocks as a text list
     */
    protected function _outputBlocks()
    {
        $blocks = func_get_args();
        $output = $this->getLayout()->createBlock('adminhtml/text_list');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        
        $this->getResponse()->setBody($output->toHtml());
    }
    
    /**
     * Get specified tab grid
     */
	public function gridOnlyAction()
    {
        $this->_initAction();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('dcontent/adminhtml_templates_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
                ->toHtml()
        );
    }
}