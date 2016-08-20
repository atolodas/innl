<?php
class Neklo_ABTesting_Adminhtml_Neklo_Abtesting_VisitorController extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('visitor');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select visitor records.'));
        } else {
            if (!empty($ids)) {
                try {
                    foreach ($ids as $id) {
                        $model = Mage::getSingleton('neklo_abtesting/visitor')->load($id);
                        $model->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($ids))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massBanAction()
    {
        $ids = $this->getRequest()->getParam('visitor');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select visitor records.'));
        } else {
            if (!empty($ids)) {
                try {
                    foreach ($ids as $id) {
                        $model = Mage::getSingleton('neklo_abtesting/visitor')->load($id);
                        $model->setIsBanned(1)->save();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were banned.', count($ids))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massUnBanAction()
    {
        $ids = $this->getRequest()->getParam('visitor');
        if (!is_array($ids)) {
            $this->_getSession()->addError($this->__('Please select visitor records.'));
        } else {
            if (!empty($ids)) {
                try {
                    foreach ($ids as $id) {
                        $model = Mage::getSingleton('neklo_abtesting/visitor')->load($id);
                        $model->setIsBanned(0)->save();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) were un banned.', count($ids))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }
}
