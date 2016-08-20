<?php

class Cafepress_CPCore_Model_Xmlformat_Format_Transformer extends Cafepress_CPCore_Model_Xmlformat_Format_Abstract {
	protected $_serverResponse = false;
	protected $_format = false;

	public function getTemplate() {
		return Mage::getModel('cpcore/template')->setStoreId($this->getStoreId());
	}

	public function processResponse($cap = false) {
		$result = NULL;
		$methodResponse = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_Responsemethods')->getNameTypeById($this->getResponseMethod());
		switch ($methodResponse) {
			case 'return_result':{
					if (!Mage::helper('cpcore')->isXML($this->_serverResponse)) {
						return false;
					}
					try {
						$this->responseDOM = new SimpleXMLElement($this->_serverResponse);
						$this->responseFormatDOM = new SimpleXMLElement($this->_getResponse());
					} catch (Exception $e) {$result = $e->getMessage() . ", SORRY";return false;	}
					$this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
					$result = Mage::getSingleton('cpcore/xmlformat_format_entity_result')->getResult();

				}break;
			case 'return_result_simple':{
					$result = $this->_serverResponse;
				}break;
			case 'edit_result_return_all':{
					if (!Mage::helper('cpcore')->isXML($this->_serverResponse)) {
						return false;
					}
					$this->responseDOM = new SimpleXMLElement($this->_serverResponse);
					$this->responseFormatDOM = new SimpleXMLElement($this->getResponse());

					Mage::getSingleton('cpcore/xmlformat_format_entity_edit')->setIncomingXmlObj($this->responseDOM);
					$changes = $this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
					$result = Mage::getSingleton('cpcore/xmlformat_format_entity_edit')->getResult();
					return $result;
				}break;
			case 'return_data':{
					if (!Mage::helper('cpcore')->isXML($this->getRequest())) {
						return false;
					}
					$storeId = $this->getStoreId();
					$template = Mage::getModel('cpcore/template')->setStoreId($storeId);
					$this->setOutXml($this->getRequest());
					$template->setXmlformat($this);
					$vars = $this->getVariables();

					if (Mage::registry('order_id')) {
						$order = Mage::getModel('cpcore/sales_order')->load(Mage::registry('order_id'));
						$vars['order'] = $order;
					}

					$result = $this->substitutionVarsToXml('', $template, $vars);
					$result = str_replace('<![CDATA[', '', $result);
					$result = str_replace(']]>', '', $result);
					Mage::unregister('order_id');
					return $result;
				}break;
			case 'mashape':{
					$respArr = json_decode($this->_serverResponse->__get('raw_body'), true);
					echo $response = Mage::helper('cpcore')->arrayToXml($respArr)->saveXml();

					if (!Mage::helper('cpcore')->isXML($response)) {
						echo "Response is Not Xml";return false;
					}

					try {
						$this->responseDOM = new SimpleXMLElement($response);
						$this->responseFormatDOM = new SimpleXMLElement($this->_getResponse());
					} catch (Exception $e) {$result = $e->getMessage() . ", SORRY";return false;	}

					$this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
					$result = Mage::getSingleton('cpcore/xmlformat_format_entity_result')->getResult();
				}break;
			case 'json':{
					echo $this->_serverResponse;
					$respArr = json_decode($this->_serverResponse, true);
					echo $response = Mage::helper('cpcore')->arrayToXml($respArr)->saveXml();

					if (!Mage::helper('cpcore')->isXML($response)) {
						echo "Response is Not Xml";return false;
					}

					try {
						$this->responseDOM = new SimpleXMLElement($response);
						$this->responseFormatDOM = new SimpleXMLElement($this->_getResponse());
					} catch (Exception $e) {$result = $e->getMessage() . ", SORRY";return false;	}

					$this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
					$result = Mage::getSingleton('cpcore/xmlformat_format_entity_result')->getResult();
				}break;

		}
		return $result;
	}

	public function getServerResponse() {
		return $this->_serverResponse;
	}
	public function setServerResponse($xml) {
		$this->_serverResponse = $xml;
		return $this;
	}

	public function getModelformatByName($name, $storeId = false) {
		if (!$storeId) {
			$storeId = $this->getStoreId();
		}
		$xmlformatModel = $this
			->setStoreId($storeId)
			->loadByAttribute('name', trim($name));

		$xmlformatModel = $this
			->setStoreId($storeId)
			->load($xmlformatModel->getId());

		if (!$xmlformatModel->getName()) {
			die('No format With name "' . $name . '"!');
		}

		return $xmlformatModel;
	}

	public function substitutionVarsToXml($xml, $template, $vars = array()) {
		Varien_Profiler::start("xmlformat_template_proccessing");
		$templateProcessed = $template->getProcessedTemplate($vars, true);
		Varien_Profiler::stop("xmlformat_template_proccessing");
		$xml .= $templateProcessed;
		return $xml;
	}

	public function repleasePatternString($string) {
		$string = Mage::helper('cpcore/xml')->removeSpaceFromXML($string);
		$template = $this->getTemplate();
		$this->setOutXml($string);
		$template->setXmlformat($this);
		$vars = $this->getVariables();
		$name = $this->substitutionVarsToXml('', $template, $vars);
		return $name;
	}

	public function processRequest() {
		$methodRequest = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_Requestmethods')->getNameTypeById($this->getRequestMethod());
		switch ($methodRequest) {
			case 'simple_url':{
					$url = $this->repleasePatternString($this->getUrlRequest());
					echo "Request goes to " . $url;
					$this->_serverResponse = Mage::getModel('cpcore/xmlformat_outbound')->getResponseByUrl($url);
				}break;
			case 'curl':{
					$url = $this->repleasePatternString($this->getUrlRequest());
					$body = $this->repleasePatternString($this->getRequest());
					list($key, $val) = explode('=>', $body);
					$bodyArray[$key] = $val;
					echo "Request goes to " . $url;
					$this->_serverResponse = Mage::getModel('cpcore/xmlformat_outbound')->getResponseByCurl($url, $bodyArray);
				}break;
			case 'soap':{
					$xml = $this->repleasePatternString($this->getRequest());
					$this->setRequest($xml);
					$url = $this->repleasePatternString($this->getUrlRequest());
					$this->_serverResponse = Mage::getModel('cpcore/xmlformat_outbound')->getResponseOverSoap($url, $this->getPatternRequest(), $xml);
				}break;
			case 'http':{
					$xml = $this->repleasePatternString($this->getRequest());
					$this->setRequest($xml);
					$url = $this->repleasePatternString($this->getUrlRequest());
					$this->_serverResponse = Mage::getModel('cpcore/xmlformat_outbound')->sendXmlOverPost($xml, $this->getStoreId(), array('url' => $url));
				}break;
		}
		return $this;
	}

	public function checkCondition() {
		if (!$this->getCondition()) {
			return true;
		}

		$xml = $this->repleasePatternString($this->getCondition());
		if ($xml) {return true;}
		return false;
	}

	public function setRemoteFormatData($data) {
		parent::setRemoteFormatData($data);
		$data = array();
		$data['url_request'] = $this->getRemoteFormatDataAdditional('url_request');
		$data['request'] = $this->getRemoteFormatData('request_body');
		$data['pattern_request'] = $this->getRemoteFormatDataAdditional('pattern_request');
		$data['response'] = $this->getRemoteFormatData('response_body');
		$data['pattern_response'] = $this->getRemoteFormatDataAdditional('pattern_response');
		$data['request_method'] = $this->getRemoteFormatDataAdditional('request_method');
		$data['response_method'] = $this->getRemoteFormatDataAdditional('response_method');
		$this->setData($data);
		return $this;
	}
}