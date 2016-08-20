<?php
class Cafepress_CPCore_Model_Xmlformat_Outbound extends Cafepress_CPCore_Model_Abstract
{
    private $_methods = array(
                               '1'  => array(
                                                'label' => 'Sent File By FTP',
                                                'method' => 'sentFileByFtp',
                                                ),
                               '2'  => array(
                                                'label' => 'Sent File By Http',
                                                'method' => 'sentFileByHttp',
                                                ),
							   '3'  => array(
                                                'label' => 'Sent File By SOAP',
                                                'method' => 'sentFileBySoap',
                                                ),
                            );

    const XMLS      = 'xmls';
    const INBOUND   = 'inbound';
    const OUTBOUND  = 'outbound';

    protected $_xmlsPath = '';

    public function __construct() {
        parent::__construct();
        $this->_xmlsPath = Mage::getBaseDir('media').'/'.self::XMLS.'/';
    }

        /**
     * Retrieve option array
     *
     * @return array
     */
    public function getAllMethods()
    {
        $res = array();

        foreach ($this->_methods as $index => $method) {
            $res[] = array(
               'value' => $method['method'],
               'label' => $method['label']
            );
        }
        return $res;
    }

    public function outboundFile($xml, $order, $formatId, $creditmemo = false, $custom_url_or_function = false){
        Mage::log('**START OUTBOUN FILE***', null, 'debug_orderformat.log');
        $_SESSION['cp_log_request'] = $xml;
        $storeId = $order->getStoreId();
        if (!$creditmemo){
            Mage::log('**FILE:', null, 'debug_orderformat.log');
            $file = $this->saveXml($xml,$order, $formatId);
            Mage::log($file, null, 'debug_orderformat.log');
            if($file) {
                $filename = basename($file);
                $order->setData('cp_wms_file',str_replace($filename,'',$order->getData('cp_wms_file')).' '.$filename);
                $order->setCpWmsFileStatus(Mage::helper('cpcore')->getStatusId('Created'));
                $order->save();
            }
        } else {
            $file = $this->saveCreditMemoXml($xml,$order,$creditmemo, $formatId);
            if($file) {
                $filename = basename($file);
                $creditmemo->setData('cp_wms_file',$creditmemo->getData('cp_wms_file').' '.$filename);
                $creditmemo->setCpWmsFileStatus(Mage::helper('cpcore')->getStatusId('Created'));
                $creditmemo->save();
            }

        }
        $useMethods = Mage::getStoreConfig('common/format/outbound_methods', $storeId);
        Mage::log('**OUT METHODs:'.$useMethods, null, 'debug_orderformat.log');
        $methodsAr = explode(',', trim($useMethods));
        foreach($methodsAr as $method){
            switch ($method){
                case 'sentFileByFtp':{
                    if (!$creditmemo){
                        $this->sentFileByFTP($file, $order);
                    } else {
                        $this->sendCreditmemoXmlFile($file, $order, $creditmemo);
                    }
                    $response = true;
                } break;
                case 'sentFileByHttp':{
                    $response = $this->sendXmlOverPost($xml, $storeId,$custom_url_or_function);
                    $_SESSION['cp_log_response'] = $response;
                    if (!$creditmemo) {
                        $order->setCpWmsFileStatus(Mage::helper('cpcore')->getStatusId('Sent'));
                        $order->save();
                        Mage::log('Order #'.$order->getIncrementId().' sent. Response:'.$response,null,'orders.log');
                    } else {
//                        $this->sendXmlOverPost($xml,$order->getStoreId());
                        $creditmemo->setCpWmsFileStatus(Mage::helper('cpcore')->getStatusId('Sent'));
//                        $creditmemo->save();
                        $order->save();
                        Mage::log('CreditMemo for Order#'.$order->getIncrementId().' sent. Response:'.$response,null,'orders.log');
                    }

                } break;
                case 'sentFileBySoap':{
                    Mage::log('**SOAP', null, 'debug_orderformat.log');
                    Mage::log('**RESPONSE:', null, 'debug_orderformat.log');
                    $response = $this->sendXmlOverSoap($xml, $storeId, $order,$custom_url_or_function['url']);
                    Mage::log($response, null, 'debug_orderformat.log');
                    $_SESSION['cp_log_response'] = $response;
                    if (!$creditmemo){
                        $order->setCpWmsFileStatus(Mage::helper('cpcore')->getStatusId('Sent'));
                        $order->save();
                        Mage::log('Order #'.$order->getIncrementId().' sent. Response:'.$response,null,'orders.log');
                    } else {
//                        $this->sendXmlOverPost($xml,$order->getStoreId());
                        $creditmemo->setCpWmsFileStatus(Mage::helper('cpcore')->getStatusId('Sent'));
//                        $creditmemo->save();
                        $order->save();
                        Mage::log('CreditMemo for Order#'.$order->getIncrementId().' sent. Response:'.$response,null,'orders.log');
                    }
					return $response;
                } break;
            }
            return $response; #TODO INL: return all response (array?) if select several ways send
        }
    }

    public function sentFileByFTP($file,$order = false,$creditmemo = false, $nameFileFromServer = false){
        $_SESSION['cp_log_function'] = 'FTP';
        try {
            $server = trim(Mage::getStoreConfig('ftp/orders/address'));
            $login = trim(Mage::getStoreConfig('ftp/orders/login'));
            $password = trim(Mage::getStoreConfig('ftp/orders/password'));
            $folder = trim(Mage::getStoreConfig('ftp/orders/inbound'));
            $method = trim(Mage::getStoreConfig('ftp/methods/connection'));

            if ($nameFileFromServer){
                $filename = $nameFileFromServer;
            } else {
                $filename = substr($file,strrpos($file,'/')+1);
            }

//            $fienameComplete = "$method://$server/$folder/".$filename;
            $fienameComplete = "ftp://$server/$folder/".$filename;

            $_SESSION['cp_log_url_of_request'] = $fienameComplete;
            $localfile = $file;

            $fp = fopen($localfile, 'r');

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $fienameComplete);
            curl_setopt($ch, CURLOPT_UPLOAD, 1);
            curl_setopt($ch, CURLOPT_INFILE, $fp);
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            //SSL stuff
            if ($method=='ftps') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
                curl_setopt($ch, CURLOPT_FTPSSLAUTH, 1);
                curl_setopt($ch, CURLOPT_SSLVERSION, 3);
            }
            //end SSL
            curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localfile));
            curl_exec($ch);
            $error_no = curl_errno($ch);
            curl_close($ch);

            if ($order!=false){
                if (!$creditmemo){
                   if ($error_no==0) {
                        $error = false;
                        $order->setData('cp_wms_file_status',(Mage::helper('cpcore')->getStatusId('Sent')));
                        $order->save();
                    } else {
                        $error = 'File upload error. '.$fienameComplete;
                        $order->setData('cp_wms_file_status',(Mage::helper('cpcore')->getStatusId('Fail Sending')));
                        $order->save();
                    }
                } else {
                    if ($error_no==0) {
                        $error = false;
                        $creditmemo->setData('cp_wms_file_status',(Mage::helper('cpcore')->getStatusId('Sent')));
                        $creditmemo->save();
                    } else {
                        $error = 'File upload error. '.$fienameComplete;
                        $creditmemo->setData('cp_wms_file_status',(Mage::helper('cpcore')->getStatusId('Fail Sending')));
                        $creditmemo->save();
                    }
                }

            }
            if ($error){
                Mage::log($error.' '.$error_no.' '.$filename,null,'orders.log');
            }

        } catch (Exception $e) {
            if (!$creditmemo){
                Mage::log('Order XML not uploaded. '.$e->getMessage(),null,'orders.log');
            } else {
                Mage::log('Creditmemo XML not uploaded. '.$e->getMessage(),null,'orders.log');
            }

        }
    }

    public function sendCreditmemoXmlFile($file,$order,$creditmemo) {
        $_SESSION['cp_log_function'] = 'FTP';
        return $this->sentFileByFTP($file, $order, $creditmemo);
    }

    /**
     *
     * @param type $xml
     * @param type $server : $server = array('url'=>$url, 'username'=>$username, 'password'=>$password)
     * @return type
     */
    public function sendXmlOverPost($xml, $storeId = 0,$server = array())
    {
        $_SESSION['cp_log_function'] = 'HTTP';
        if (!isset($server['url']) || !$server['url']){
            $server['url'] = Mage::getStoreConfig('http/orderstatus/server', $storeId);
        }
        if (!isset($server['username']) || !$server['username']) {
                $server['username'] = Mage::getStoreConfig('http/orderstatus/username', $storeId);
        }
        if (!isset($server['password']) || !$server['password']){
                $server['password'] = Mage::getStoreConfig('http/orderstatus/password', $storeId);
        }
        $_SESSION['cp_log_url_of_request'] = $server['url'];

        Mage::log('request goes to '.$server['url'],null,'orders.log');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $server['url']);
        curl_setopt($ch, CURLOPT_USERPWD, $server['username'].':'.$server['password']);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function simplexml_to_array($xmlObject)
    {
        $out = array();
        foreach((array)$xmlObject as $index => $node)
        {
            if(is_object($node)) $out[$index] = $this->simplexml_to_array($node);
            else $out[$index] = $node;
        }
        return $out;
    }

	function _addNodeToXML(&$xml,$nodeName,$nodeValue,$open=0){
		if(is_array($nodeValue)){
//			$xml->startElement($nodeName);
//			foreach($nodeValue as $nodeValueKey => $nodeValueValue){
//			$this->_addNodeToXML($xml, $nodeValueKey, $nodeValueValue);
//		}
//			$xml->endElement();
		} elseif (is_a($nodeValue,'stdClass')){
//		if(!$open) { $xml->startElement($nodeValueKey); $open++; }
			foreach($nodeValue as $nodeValueKey => $nodeValueValue){
			$this->_addNodeToXML($xml, $nodeValueKey, $nodeValueValue,$open);
			}

		}else{
			if($nodeName!='logPath') {
				$xml->writeElement($nodeName,$nodeValue);
			}
		}
	}

    public function sendXmlOverSoap($xml, $storeId = 0, $order = false, $custom_url_or_function=false, $server = array())
    {
        $_SESSION['cp_log_function'] = $custom_url_or_function;
		if($order != false)
		{
			$_SESSION['number'] = $order->getIncrementId();
			if(!Mage::registry('number')) {
				Mage::register('number',$order->getIncrementId());
			}
		}
        if (!isset($server['url']) || !$server['url']){
            if (Mage::getStoreConfig('soap/options/server', $storeId)){
                $server['url'] = Mage::getStoreConfig('soap/options/server', $storeId);
            } else {
                $server['url'] = Mage::getStoreConfig('http/orderstatus/server', $storeId);
            }
        }
        $_SESSION['cp_log_url_of_request'] = $server['url'];
        if (!isset($server['username']) || !$server['username']) {
            $server['username'] = Mage::getStoreConfig('http/orderstatus/username', $storeId);
        }
        if (!isset($server['password']) || !$server['password']){
            $server['password'] = Mage::getStoreConfig('http/orderstatus/password', $storeId);
        }

		Mage::log('request goes to '.$server['url'],null,'orders.log');
		$options = array('soap_version' => SOAP_1_1, 'exceptions'=>true, 'trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE);
        $soap = new SoapClient($server['url']);
		$sxml = new SimpleXMLElement($xml);
		$order_params = array();
		$order_params = $this->simplexml_to_array($sxml, $order_params);
$result = $soap->$custom_url_or_function($order_params);


		$mxml = new XMLWriter();
		$mxml->openMemory();
		$mxml->startElement($custom_url_or_function.'Result');
		$this->_addNodeToXML($mxml, 'root', $result);
		$mxml->endElement();
		$response = Mage::helper('cpcore')->replaceCharsXML($mxml->outputMemory());

		return $response;
    }

    public function checkFolders() {
        $xmls_folder        = $this->_xmlsPath;
        $xmls_in_folder     = $this->_xmlsPath.self::INBOUND.'/';
        $xmls_out_folder    = $this->_xmlsPath.self::OUTBOUND.'/';
        if(!is_dir($xmls_folder)) {
            mkdir($xmls_folder);
            chmod($xmls_folder,0777);
        }
        if(!is_dir($xmls_out_folder)) {
            mkdir($xmls_out_folder);
            chmod($xmls_out_folder,0777);
        }
        if(!is_dir($xmls_in_folder)) {
            mkdir($xmls_in_folder);
            chmod($xmls_in_folder,0777);
        }
    }

    public function deleteFilesFromFtp($files = array()){
        if (!is_array($files) || (count($files)<=0)){
            Mage::log('No fies From delete!',null,'wms.log');
            return false;
        }

        $server = trim(Mage::getStoreConfig('ftp/orders/address'));
        $login = trim(Mage::getStoreConfig('ftp/orders/login'));
        $password = trim(Mage::getStoreConfig('ftp/orders/password'));
        $folder = trim(Mage::getStoreConfig('ftp/orders/outbound'));
        $method = trim(Mage::getStoreConfig('ftp/methods/connetcion'));

        if (($folder!='') && ($folder[strlen($folder)-1]!='/')){
            $folder = $folder.'/';
        }

        foreach($files as $key=>$fileName){
            $files[$key] = substr($fileName,strrpos($fileName,'/')+1);
        }

        $filesFromDelete = array();
        foreach($files as $fileName){
            $filesFromDelete[] = '/'.$folder.$fileName;
        }

        Mage::log('Connect to FTP:'.$login.'@'.$server.'/'.$folder,null,'wms.log');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "ftp://$server/$folder/");
        curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //SSL stuff
        if ($method=='ftps') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
            curl_setopt($ch, CURLOPT_FTPSSLAUTH, 1);
            curl_setopt($ch, CURLOPT_SSLVERSION, 3);
        }

        curl_setopt($ch, CURLOPT_FTPLISTONLY, 1);
        curl_setopt($ch, CURLOPT_QUOTE, $filesFromDelete);

        curl_error($ch);
        $return = curl_exec($ch);
        Mage::log("File:".implode(', ',$files)." was deleted with message:".$return,null,'wms.log');
        echo "File:".implode(', ',$files)." was deleted with message:".$return.'<br>';
        curl_close ($ch);
        return $return;
    }

    public function getResponseByUrl($url, $verbose=false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($verbose == TRUE)
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        $result= curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    public function getResponseByCurl($url,$params, $verbose=false)
    {
        $result = Unirest::get(
          $url,
          $params,
          null
        );
        return $result;
    }

	function _addNodeToXML2(&$xml,$nodeName,$nodeValue,$open=0){
		if(is_array($nodeValue)){
			$xml->startElement($nodeName);
			foreach($nodeValue as $nodeValueKey => $nodeValueValue){
				$this->_addNodeToXML2($xml, $nodeValueKey, $nodeValueValue);
			}
			$xml->endElement();
		} elseif (is_a($nodeValue,'stdClass')){
			foreach($nodeValue as $nodeValueKey => $nodeValueValue){
				$this->_addNodeToXML2($xml, $nodeValueKey, $nodeValueValue, $open);
			}
		}else{
			if($nodeName!='logPath') {
				$xml->writeElement($nodeName,$nodeValue);
			}
		}
	}

	public function getResponseOverSoap($url, $func, $xml)
    {
		$options = array('soap_version' => SOAP_1_1, 'exceptions'=>true, 'trace' => 1, 'cache_wsdl' => WSDL_CACHE_NONE);
		$soap = new SoapClient($url);
		$sxml = new SimpleXMLElement($xml);
		$order_params = array();
		$order_params = $this->simplexml_to_array($sxml, $order_params);
		$result = $soap->$func($order_params);
		$mxml = new XMLWriter();
		$mxml->openMemory();
		$mxml->startElement($func.'Result');
		$this->_addNodeToXML2($mxml, 'root', $result);
		$mxml->endElement();
		$response = Mage::helper('cpcore')->replaceCharsXML($mxml->outputMemory());
		return $response;
	}

    public function getXmlsPath()
    {
        return $this->_xmlsPath;
    }

    public function getFileNameWithoutPath($fullPath)
    {
        return str_replace($this->_xmlsPath, '', $fullPath);
    }

    public function saveXml($xml, $order, $formatId) {
        $orderModel = Mage::getModel('cpcore/xmlformat_format_order')
            ->setStoreId($order->getStoreId())
            ->setOrderById($order->getId())
            ->setFormat($formatId);
        $saveFilename = $orderModel->getSaveFilename();

        $this->checkFolders();
        if ($saveFilename != ''){
            $file = Mage::getBaseDir().'/media/'.self::XMLS.'/'.self::OUTBOUND.'/'.$saveFilename;
            $file_path = self::OUTBOUND.'/'.$saveFilename;
        } else {
            $file = Mage::getBaseDir().'/media/'.self::XMLS.'/'.self::OUTBOUND.'/PO-'.date('m-d-Y-').$order->getIncrementId().'.xml';
            $file_path = self::OUTBOUND.'/PO-'.date('m-d-Y-').$order->getIncrementId().'.xml';
        }

        $_SESSION['cp_log_link_to_file'] = $file_path;
        //@unlink($file);
        $resource = fopen($file,"a+");
        if(fwrite($resource, $xml)===FALSE) {
            Mage::log('Can\'t save file '.$file.' for order '.$order->getIncrementId(),null,'orders.log');
            return false;
        } else {
            chmod($file, 0777);
            fclose($resource);
            return $file;
        }
    }

    public function saveCreditMemoXml($xml,$order,$creditmemo, $formatId) {
        $type = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getType('creditmemo');
        $creditmemoModel = Mage::getModel('cpcore/xmlformat_format_creditmemo')
            ->setStoreId($order->getStoreId())
//                            ->setOrderById($order->getId())
            ->setOrder($order)
            ->setCreditmemo($creditmemo)
            ->setFormat($formatId);
        $saveFilename = $creditmemoModel->getSaveFilename();

        $this->checkFolders();
        if ($saveFilename != ''){
            $file = $this->_xmlsPath.self::OUTBOUND.'/'.$saveFilename;
            $file_path = self::OUTBOUND.'/'.$saveFilename;
        } else {
            $file = $this->_xmlsPath.self::OUTBOUND.'/CM-'.date('m-d-Y-').$creditmemo->getIncrementId().'.xml';
            $file_path = self::OUTBOUND.'/CM-'.date('m-d-Y-').$creditmemo->getIncrementId().'.xml';
        }

        $_SESSION['cp_log_link_to_file'] = $file_path;
        @unlink($file);
        $resource = fopen($file,"a+");
        if(fwrite($resource, $xml)===FALSE) {
            Mage::log('Can\'t save file '.$file.' for order '.$order->getIncrementId(),null,'orders.log');
            return false;
        } else {
            Mage::log('File '.$file.' for order '.$order->getIncrementId().' saved',null,'orders.log');
            chmod($file, 0777);
            fclose($resource);
            return $file;
        }
    }

    private function fileNotOpen($filename, $logName = 'wms.log')
    {
        Mage::log('File:'.$filename.' - Can\'t open.',null,$logName);
        die('Can\'t open file');
    }

    public function getFiles($pattern, $type = 'TYPE_FILE') {
        $this->checkFolders();
        $files = array();
        try {
            $server = trim(Mage::getStoreConfig('ftp/orders/address'));
            $login = trim(Mage::getStoreConfig('ftp/orders/login'));
            $password = trim(Mage::getStoreConfig('ftp/orders/password'));
            $folder = trim(Mage::getStoreConfig('ftp/orders/outbound'));
            $method = trim(Mage::getStoreConfig('ftp/methods/connetcion'));

//            Mage::log('Connect to FTP:'.$login.'@'.$server.'/'.$folder,null,'wms.log');
            $_SESSION['cp_log_url_of_request'] = "ftp://$server/$folder/";

            $ch = curl_init();
echo "requesting folder ftp://$server/$folder/ $login:$password <br/><br/> ";
            curl_setopt($ch, CURLOPT_URL, "ftp://$server/$folder/");
            curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            //SSL stuff
            if ($method=='ftps') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
                curl_setopt($ch, CURLOPT_FTPSSLAUTH, 1);
                curl_setopt($ch, CURLOPT_SSLVERSION, 3);
            }

            curl_error($ch);
            $return = curl_exec($ch);
echo "Found resources: <br/>";
            foreach(explode(PHP_EOL,$return) as $line) {
		echo $line."<br/>";
		foreach(explode(' ',$line) as $item) {
                    if(substr_count($item,'.xml') && preg_match($pattern,$item)) {
                        $items[] = $item;
                    }
                }
            }
            curl_close ($ch);

            if(empty($items)) {
                Mage::log('No files by pattern:'.$pattern.' to import',null,'wms.log');
                echo 'No files by pattern:'.$pattern.' to import';
		echo "Continue";              
 // return false;
            }

            $local_dir = $this->_xmlsPath.self::INBOUND.'/';
            $local_files = scandir($local_dir);

            $downloads_file = $local_files; // array_diff($items,$local_files);
  foreach($downloads_file as $item) {
		if($item == '.' || $item == '..') continue;
                $local_file = $local_dir.$item;
              $files[] = $local_file;
	/*	//   @unlink($local_file);
                $fh = fopen($local_file, 'w') or $this->fileNotOpen($local_file);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_FILE, $fh);
                curl_setopt($ch, CURLOPT_URL, "ftp://$server/$folder/$item");
                curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
                //SSL stuff
                if ($method=='ftps') {
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($ch, CURLOPT_FTP_SSL, CURLFTPSSL_TRY);
                    curl_setopt($ch, CURLOPT_FTPSSLAUTH, 1);
                    curl_setopt($ch, CURLOPT_SSLVERSION, 3);
                }
                curl_exec($ch);
                curl_close($ch);
                fclose($fh);
*/
            }
            return $files;

        } catch (Exception $e) {
            Mage::log('Files Not Download! Error:'.$e->getMessage(),null,'wms.log');
            Mage::log($e->getMessage(),null,'wms.log');
            echo 'Files Not Download! Error:'.$e->getMessage();
        }
    }
}
