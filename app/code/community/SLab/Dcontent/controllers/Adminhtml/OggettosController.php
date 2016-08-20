<?php
/**
 * Product blocks admin controller: CRUD
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Adminhtml_OggettosController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Init controller action
	 *
	 * @return this
	 */
	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('dcontent/oggettos')
			->_addBreadcrumb(Mage::helper('dcontent')->__('Oggettos Blocks Manager'), Mage::helper('dcontent')->__('Oggettos Blocks Manager'));
		
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
	 * Edit block page
	 *
	 */
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('dcontent/oggettos')->load($id);
		
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
	
			if (!empty($data)) {
				$model->setData($data);
			}				

			Mage::register('dcontent_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('dcontent/oggettos');

			$this->_addBreadcrumb(Mage::helper('dcontent')->__('Blocks Manager'), Mage::helper('dcontent')->__('Blocks Manager'));
	

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('dcontent/adminhtml_oggettos_edit'))
				->_addLeft($this->getLayout()->createBlock('dcontent/adminhtml_oggettos_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Block does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	/**
	 * New block page
	 *
	 */
	public function newAction() {
		$this->_forward('edit');
	}
 
	/**
	 * Save block
	 *
	 */
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
                $model = Mage::getModel('dcontent/oggettos');
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
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dcontent')->__('Block was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Unable to find block to save'));
        $this->_redirect('*/*/');
	}
 
	/**
	 * Delete block
	 *
	 */
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('dcontent/oggettos');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('dcontent')->__('Block was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    /**
     * Mass delete blocks
     *
     */
    public function massDeleteAction() {
        $dcontentIds = $this->getRequest()->getParam('dcontent');
        if(!is_array($dcontentIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Please select block(s)'));
        } else {
            try {
                foreach ($dcontentIds as $dcontentId) {
                    $dcontent = Mage::getModel('dcontent/oggettos')->load($dcontentId);
                    $dcontent->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('dcontent')->__(
                        'Total of %d block(s) were successfully deleted', count($dcontentIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    /**
     * Mass update blocks status
     *
     */
    public function massStatusAction()
    {
        $dcontentIds = $this->getRequest()->getParam('dcontent');
        if(!is_array($dcontentIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('dcontent')->__('Please select block(s)'));
        } else {
            try {
                foreach ($dcontentIds as $dcontentId) {
                    $dcontent = Mage::getSingleton('dcontent/oggettos')
                        ->load($dcontentId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('dcontent')->__('Total of %d block(s) were successfully updated', count($dcontentIds))
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
    	return $this->getLayout()->createBlock('dcontent/adminhtml_oggettos_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setOggettos($productsArray)
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
     * Product grid for AJAX request
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('score/adminhtml_score_oggetto_grid')->toHtml()
        );
    }
    
    /**
     * Get specified tab grid
     */
    public function gridOnlyAction()
    {
        $this->_initAction();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('dcontent/adminhtml_oggettos_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
                ->toHtml()
        );
    }
    
	/**
	 * Products grid on block edit form
	 *
	 */
	public function productsAction()
    {
        $gridBlock = $this->getLayout()->createBlock('dcontent/adminhtml_oggettos_edit_tab_oggettos')
            ->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'oggettos')));
        
        $pline = Mage::getModel('dcontent/oggettos')->load($this->getRequest()->getParam('id'))->getOggettos();
        $products = array();
        
        if($pline!='')
        { 
        	$decoded = Mage::helper('dcontent')->decodeInput($pline);
        	$products_arr = explode('&',$pline);
        	foreach($products_arr as $p) { 
        		list($id,$pos) = explode('=',$p);
        		 $product = Mage::getModel('score/oggetto')->load($id);
        		 $product->setPosition($decoded[$id]['position']);
				 $products[] = $product;
        		 
        	}
        }       
        $serializerBlock = $this->_createSerializerBlock('oggettos', $gridBlock, $products);
	    $this->_outputBlocks($gridBlock, $serializerBlock);
    }
}