<?php

class Cafepress_CPCore_Model_Log extends Mage_Core_Model_Abstract {

    protected $originalFieName = false;

    protected function _construct() {
        $this->_init('cplog/log');
    }

    public function saveToLog($observer) {
        try {
            $order = $observer->getOrder();
            $storeId = $order->getStoreId();
            if (!isset($_SESSION['cp_log_format_id'])) {
                $format = $observer->getFormat();
                $formatId = $format->getEntityId();
            } else {
                $formatId = $_SESSION['cp_log_format_id'];
            }
            $log_model = Mage::getModel('cplog/log');

            if (isset($_SESSION['cp_log_format_id'])) {
                $log_model->setFormatId($_SESSION['cp_log_format_id']);
            } else {
                $log_model->setFormatId($formatId);
            }
            $log_model->setExecutionDate(strtotime("now"));
            $log_model->setFunction($_SESSION['cp_log_function']);
            $log_model->setRequest($_SESSION['cp_log_request']);
            if (isset($_SESSION['cp_log_response']))
                $log_model->setResponse($_SESSION['cp_log_response']);

            if (isset($_SESSION['cp_log_response_format'])) {
                $log_model->setResponseFormat($_SESSION['cp_log_response_format']);
            } else {
//                $log_model->setResponseFormat($format->getResponse());
                $log_model->setResponseFormat(Mage::getModel('cpcore/xmlformat')->load($formatId)->getResponse());
            }
            if (isset($_SESSION['cp_log_status']))
                $log_model->setStatus($_SESSION['cp_log_status']);
            $log_model->setLinkToFile($_SESSION['cp_log_link_to_file']);
            $log_model->setOrderId($order->getIncrementId());
            if (isset($_SESSION['cp_log_url_of_request']))
                $log_model->setUrlOfRequest($_SESSION['cp_log_url_of_request']);

            $log_model->setWmsFiles($order->getCpWmsFile());
            $log_model->setWmsStatuses($order->getCpWmsFileStatus());

            $parentId = Mage::registry('cp_log_logparent_id');
            if ($parentId) {
                $log_model->setParentId($parentId);
            }

            $log_model->save();

            unset($_SESSION['cp_log_format_id']);
            unset($_SESSION['cp_log_url_of_request']);
            unset($_SESSION['cp_log_function']);
            unset($_SESSION['cp_log_request']);
            unset($_SESSION['cp_log_response']);
            unset($_SESSION['cp_log_response_format']);
            unset($_SESSION['cp_log_status']);
            unset($_SESSION['cp_log_link_to_file']);
        } catch (Exception $e) {
            Mage::log('Wms logger has error:' . $e->getMessage());
        }
    }

    public function orderstatus_format_performed($observer) {
        try {
            $format = $observer->getFormat();
            $logModel = Mage::getModel('cplog/log');

            $logModel->setFormatId($format->getId());
            $logModel->setExecutionDate(strtotime("now"));
            $logModel->setFunction($_SESSION['cp_log_function']);
            $logModel->setRequest($observer->getRequest());
            $logModel->setResponse($observer->getResponse());
            $logModel->setResponseFormat($format->getResponse());
            if (isset($_SESSION['cp_log_status']))
                $logModel->setStatus($_SESSION['cp_log_status']);
            if (isset($_SESSION['cp_log_url_of_request']))
                $logModel->setUrlOfRequest($_SESSION['cp_log_url_of_request']);
            $logModel->setProtocolOfRequest('sentFileByHttp');
            $parentId = Mage::registry('cp_log_logparent_id');
            if ($parentId) {
                $logModel->setParentId($parentId);
            }

            $logModel->save();

            unset($_SESSION['cp_log_url_of_request']);
            unset($_SESSION['cp_log_function']);
            unset($_SESSION['cp_log_status']);
        } catch (Exception $e) {
            Mage::log('Wms logger has error:' . $e->getMessage());
        }
    }

    public function downfileparsresp_format_performed($observer) {
        try {
            $format = $observer->getFormat();

            foreach ($format->getFilesContent() as $file) {
                $logModel = Mage::getModel('cplog/log');

                $logModel->setFormatId($format->getId());
                $logModel->setExecutionDate(strtotime("now"));
                if (isset($_SESSION['cp_log_function']))
                    $logModel->setFunction($_SESSION['cp_log_function']);
                $logModel->setRequest($file['content']);
//                $logModel->setResponse($format->getResponse());
                $logModel->setResponseFormat($format->getResponse());
                $logModel->setLinkToFile(Mage::getSingleton('cpcore/xmlformat_outbound')->getFileNameWithoutPath($file['filename']));

                if (isset($_SESSION['cp_log_status']))
                    $logModel->setStatus($_SESSION['cp_log_status']);
                if (isset($_SESSION['cp_log_url_of_request']))
                    $logModel->setUrlOfRequest($_SESSION['cp_log_url_of_request']);
                $parentId = Mage::registry('cp_log_logparent_id');
                if ($parentId) {
                    $logModel->setParentId($parentId);
                }

                $logModel->save();
            }
            unset($_SESSION['cp_log_url_of_request']);
            unset($_SESSION['cp_log_function']);
            unset($_SESSION['cp_log_status']);
        } catch (Exception $e) {
            Mage::log('Wms logger has error:' . $e->getMessage());
        }
    }

    public function resend($formatData) {
        $_SESSION['cp_log_format_id'] = $formatData['format_id'];
        $_SESSION['cp_log_url_of_request'] = $formatData['url_of_request'];
        $_SESSION['cp_log_condition'] = $formatData['condition'];
        $_SESSION['cp_log_function'] = $formatData['function'];
        $_SESSION['cp_log_request'] = $formatData['request'];
//        $_SESSION['cp_log_response'] = $formatData['response'];
        $_SESSION['cp_log_response_format'] = $formatData['response_format'];
//        $_SESSION['cp_log_status'] = $formatData['status'];
        try {
            $order = Mage::getModel('sales/order')->loadByIncrementId($formatData['order_id']);

            if ($formatData['function'] == 'FTP') {
                $full_file_path = $this->getFullPathToFile($formatData['link_to_file']);

                $_SESSION['cp_log_link_to_file'] = $this->getSimpleFilename($full_file_path);

                $this->saveInFile($full_file_path, $formatData['request']);
                Mage::getModel('cpcore/xmlformat_outbound')->sentFileByFTP($full_file_path, $order, false, $this->originalFieName);
            } elseif ($formatData['function'] == 'HTTP') {
                $full_file_path = $this->getFullPathToFile($formatData['link_to_file']);
                $this->saveInFile($full_file_path, $formatData['request']);

                $_SESSION['cp_log_link_to_file'] = $this->getSimpleFilename($full_file_path);

                $response = Mage::getModel('cpcore/xmlformat_outbound')->sendXmlOverPost($formatData['request'], 0, array('url' => $formatData['url_of_request']));
                $_SESSION['cp_log_response'] = $response;

                if ($response) {
                    Mage::getModel('cpcore/xmlformat_format_order')->processResponseByRequest($response, $formatData['response_format']);
                }
            }
//        elseif($formatData['function'] == 'SOAP')
            else {
                $full_file_path = $this->getFullPathToFile($formatData['link_to_file']);
                $this->saveInFile($full_file_path, $formatData['request']);

                $_SESSION['cp_log_link_to_file'] = $this->getSimpleFilename($full_file_path);

                $response = Mage::getModel('cpcore/xmlformat_outbound')->sendXmlOverSoap($formatData['request'], false, false, $formatData['function'], array('url' => $formatData['url_of_request']));
                $_SESSION['cp_log_response'] = $response;
                if ($response) {
                    Mage::getModel('cpcore/xmlformat_format_order')->processResponseByRequest($response, $formatData['response_format']);
                }
            }

            Mage::dispatchEvent('order_format_preperformed_action', array('order' => $order));
        } catch (Exception $e) {
            Mage::log('Wms logger has error:' . $e->getMessage());
            $session = Mage::getSingleton("customer/session");
            $session->addError($e->getMessage());
            if (Mage::registry('cp_log_logparent_id')) {
                $this->_redirectUrl('*/*/request/log_id/' . Mage::registry('cp_log_logparent_id') . '/');
            } else {
                $this->_redirectUrl('*/*/index/');
            }
        }
    }

    public function getFilenameAndPostfixIncrement($filename) {
        $result = $filename;
        $filenameSimple = $this->getSimpleFilename($filename);
        $this->originalFieName = $filenameSimple;

        $path = str_replace($filenameSimple, '', $filename);
        $nameAsArray = explode('.', $filename);

        if (count($nameAsArray) > 1) {
            $lastEl = '.' . end($nameAsArray);
            $nameEl = str_replace($lastEl, '', $filenameSimple);
        } else {
            $lastEl = '';
            $nameEl = $filenameSimple;
        }
        $files = glob($path . "*$nameEl*", GLOB_NOSORT);
        if (count($files) == 0) {
            return $result;
        }

        $max = 0;
        foreach ($files as $file) {
            preg_match('/\((?P<inc>\d+)\)(?P<post>.*)/', $file, $matches);
            if (isset($matches['inc'])) {
                if (is_numeric($matches['inc'])) {

                    if ($max < $matches['inc']) {
                        $max = $matches['inc'];
                    }
                }
            }
        }
        $increment = ++$max;

        if ($increment > 1) {
            preg_match('/\((?P<inc>\d+)\)(?P<post>.*)/', $nameEl, $matches);
            if (isset($matches[0])) {
                $increment_part = '.' . $matches[0];
            } else {
                $increment_part = '';
            }
            $resultName = str_replace($increment_part, '', $nameEl) . ".($increment)" . $lastEl;
        } else {
            $resultName = $nameEl . ".($increment)" . $lastEl;
        }
        $result = $path . $resultName;
        return $result;
    }

    protected function getSimpleFilename($filenameWithPath) {
        $filenameSimple = substr($filenameWithPath, strrpos($filenameWithPath, '/') + 1);
        return $filenameSimple;
    }

    public function getFullPathToFile($filename, $additionaDir = 'outbound') {
        if (strpos($filename, $additionaDir . '/') === false) {
            $full_file_path = Mage::getBaseDir() . '/media/xmls/outbound/' . $filename;
        } else {
            $full_file_path = Mage::getBaseDir() . '/media/xmls/' . $filename;
        }
        $full_file_path = $this->getFilenameAndPostfixIncrement($full_file_path);
        return $full_file_path;
    }

    public function deleteFromOrderLog($observer) {
        try {
            $order = $observer->getOrder();
            $format = $observer->getFormat();
            if($_SESSION['cp_log_response']){
                $this->deleteOldLogs($_SESSION['cp_log_link_to_file'], $_SESSION['cp_log_request'], $_SESSION['cp_log_response']);
            } else{
                $this->deleteOldLogs($_SESSION['cp_log_link_to_file'], $_SESSION['cp_log_request']);
            }
            Mage::dispatchEvent('order_format_performed', array('order' => $order, 'format' => $format));
        } catch (Exception $e) {
            Mage::log('Wms logger has error:' . $e->getMessage());
        }
    }

    public function deleteFromOrderStatusLog($observer){
        try {
            if($_SESSION['cp_log_response']){
                $this->deleteOldLogs($_SESSION['cp_log_link_to_file'], $observer->getRequest(), $observer->getResponse());
            } else{
                $this->deleteOldLogs($_SESSION['cp_log_link_to_file'], $observer->getRequest());
            }
            Mage::dispatchEvent('orderstatus_format_performed',
                array(
                    'format' => $observer->getFormat(),
                    'request'=> $observer->getRequest(),
                    'response'=> $observer->getResponse(),
                    'result'=> $observer->getResult()
                ));
        } catch (Exception $e) {
            Mage::log('Wms logger has error:' . $e->getMessage());
        }
    }

    public function deleteOldLogs($link_to_file = null, $request = null, $response = null){
        $logs = Mage::getModel('cplog/log')->getCollection();
        if($link_to_file){
            $logs = $logs->addFieldToFilter('link_to_file', $link_to_file);
        }
        if($request){
            $logs = $logs->addFieldToFilter('request', $request);
        }
        if($response){
            $logs = $logs->addFieldToFilter('response', $response);
        }
        if($link_to_file || $request || $response){
            foreach($logs as $log){
                $log_to_delete = Mage::getModel('cplog/log')->load($log->getId());
                $log_to_delete->delete();
            }
        }
    }

    protected function saveInFile($filename, $content) {
        @unlink($filename);
        $resource = fopen($filename, "w");
        fwrite($resource, $content);
        fclose($resource);
        return true;
    }
}
