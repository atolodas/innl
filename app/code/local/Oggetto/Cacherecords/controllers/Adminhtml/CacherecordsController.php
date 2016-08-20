<?php

class Oggetto_Cacherecords_Adminhtml_CacherecordsController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cacherecords/items')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('cacherecords/cacherecords')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('cacherecords_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('cacherecords/items');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('cacherecords/adminhtml_cacherecords_edit'))
                ->_addLeft($this->getLayout()->createBlock('cacherecords/adminhtml_cacherecords_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cacherecords')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function infoAction()
    {
        $records = Mage::getModel('cacherecords/cacherecords')->getCollection();
        foreach ($records as $record) {
            $file = $record->getData('md5key');
            $title = "Can't get title";
            if (is_file(Mage::getBaseDir() . '/var/lightspeed/' . $file)) {
                $record->setData('file_exist', 'Yes');
                $content = file_get_contents(Mage::getBaseDir() . '/var/lightspeed/' . $file);
                if (preg_match("/<title>(.+)<\/title>/i", $content, $m)) {
                    $title = $m[1];
                }
            } else {
                $record->setData('file_exist', 'No');
            }

            $record->setData('created_time', date('Y-m-d h:i:s', filemtime(Mage::getBaseDir() . '/var/lightspeed/' . $file)));


            $record->setData('title', $title);
            $record->save();
        }
        $this->_redirect('*/*/');
    }

    public function contentAction()
    {
        $records = Mage::getModel('cacherecords/cacherecords')->getCollection();
        foreach ($records as $record) {
            $file = $record->getData('md5key');
            $title = "Can't get content";
            if (is_file(Mage::getBaseDir() . '/var/lightspeed/' . $file)) {
                $record->setData('file_exist', 'Yes');
                $content = file_get_contents(Mage::getBaseDir() . '/var/lightspeed/' . $file);

            } else {
                $record->setData('file_exist', 'No');
            }


            $record->setData('content', $content);
            $record->save();
        }
        $this->_redirect('*/*/');
    }

    public function markAction()
    {

        $urls = array();
        $dir = Mage::getBaseDir('media') . "/assets/cacherecords/";
        $files = scandir($dir);
        foreach ($files as $f) {
            if (is_file($dir . $f) && substr_count($f, '.txt') > 0) {
                $file = fopen($dir . $f, 'r');
//                            echo fread($file,  filesize($dir.$f)); die;

                $urls = array_merge($urls, array(fread($file, filesize($dir . $f))));
                fclose($f);
            }
        }
        foreach ($urls as $part) {
            $urls = array_merge($urls, explode('
', $part));
        }

        $records = Mage::getModel('cacherecords/cacherecords')->getCollection();
        foreach ($records as $record) {
            $url = 'http://' . str_replace(' ', '', str_replace('_48', '', str_replace('_96', '', $record->getUrl())));
            if (in_array($url, $urls)) {
                $record->setMkeys('System');
            } else {
                $record->setMkeys('');
            }
            $record->save();
        }
        $this->_redirect('*/*/');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {

            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS;
                    $uploader->save($path, $_FILES['filename']['name']);

                } catch (Exception $e) {

                }

                //this way the name is saved in DB
                $data['filename'] = $_FILES['filename']['name'];
            }


            $model = Mage::getModel('cacherecords/cacherecords');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('cacherecords')->__('Item was successfully saved'));
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
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('cacherecords')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('cacherecords/cacherecords');


                $model->setId($this->getRequest()->getParam('id'));
                $md = $model->getMd5key();
                @unlink(Mage::getBaseDir() . '/var/lightspeed/' . $md[0] . '/' . $md[1] . '/' . $md);
                @unlink('/home/rugzilla/public_html/var/lightspeed/' . $md[0] . '/' . $md[1] . '/' . $md);
                $model->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        set_time_limit(10800);
        $cacherecordsIds = $this->getRequest()->getParam('cacherecords');
        if (!is_array($cacherecordsIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($cacherecordsIds as $cacherecordsId) {
                    $cacherecords = Mage::getModel('cacherecords/cacherecords')->load($cacherecordsId);
                    $md = $cacherecords->getMd5key();
                    @unlink(Mage::getBaseDir() . '/var/lightspeed/' . $md[0] . '/' . $md[1] . '/' . $md);
                    @unlink('/home/rugzilla/public_html/var/lightspeed/' . $md[0] . '/' . $md[1] . '/' . $md);
                    @unlink(Mage::getBaseDir() . '/var/lightspeed/' . $md);
                    @unlink('/home/rugzilla/public_html/var/lightspeed/' . $md);
                    $cacherecords->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($cacherecordsIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $cacherecordsIds = $this->getRequest()->getParam('cacherecords');
        if (!is_array($cacherecordsIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($cacherecordsIds as $cacherecordsId) {
                    $cacherecords = Mage::getSingleton('cacherecords/cacherecords')
                        ->load($cacherecordsId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($cacherecordsIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName = 'cacherecords.csv';
        $content = $this->getLayout()->createBlock('cacherecords/adminhtml_cacherecords_gridall')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName = 'cacherecords.xml';
        $content = $this->getLayout()->createBlock('cacherecords/adminhtml_cacherecords_gridall')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
