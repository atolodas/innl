<?php

class Cafepress_CPCore_Model_Xmlformat_Format_Order extends Cafepress_CPCore_Model_Xmlformat_Format_Abstract
{
    protected   $order = false;
    protected   $format = false;


    public function _construct() {
        $this->_init('cpcore/xmlformat');
        parent::_construct();
    }

	public function checkCondition()
    {
		$this->addVariable('order', $this->getOrder());

        $template = $this->getTemplate();
        $this->format->setOutXml($this->format->getCondition());
        $template->setXmlformat($this->format);
        $vars = $this->getVariables();

        $xml = $this->substitutionVarsToXml('', $template, $vars);

        if($xml)
        {
            return true;
        }
		return false;
    }

    public function generateRequest()
    {
        $template = $this->getTemplate();
        $this->setOutXml($this->getRequest());
        $template->setXmlformat($this);
        $vars = $this->getVariables();

        $xml = $this->substitutionVarsToXml('', $template, $vars);

        return $xml;
    }

    protected function getTemplate()
    {
        return Mage::getModel('cpcore/template')->setStoreId($this->getStoreId());
    }

    public function getOrder(){
        return $this->order;
    }

    public function setOrder($order){
        $this->order = $order;
        return $this;
    }

    public function setOrderById($orderId){
        $this->order = Mage::getModel('sales/order')->load($orderId);
        return $this;
    }


//    public function processResponse($responseXML)
//    {
//        try {
//            $this->responseDOM = new SimpleXMLElement($responseXML);
//            $this->responseFormatDOM = new SimpleXMLElement($this->getResponse());
//
//            $this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
//        } catch (Exception $e) {
//            Mage::log($e->getMessage(),null,'orders.log');
//            return false;
//        }
//        return true;
//    }

    public function getSaveFilename()
    {
        $this->addVariable('order', $this->getOrder());

        $template = $this->getTemplate();
        $this->format->setOutXml($this->format->getFilenameOut());
        $template->setXmlformat($this->format);
        $vars = $this->getVariables();

        $name = $this->substitutionVarsToXml('', $template, $vars);

        $name = preg_replace("/[\s]*/", '', $name);

        return $name;
    }

    public function setFormat($formatId = false){
        $type = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getType('order');
		if($formatId != false)
		{
			$this->format = Mage::getModel('cpcore/xmlformat')
					->setStoreId($this->getStoreId())
					->loadByAttributes(array(
						'type' => $type,
//						'status'=> '1',//1-enabled, 2- disabled
						'entity_id' => $formatId
					));
		}
		else
		{
			$this->format = Mage::getModel('cpcore/xmlformat')
					->setStoreId($this->getStoreId())
					->loadByAttributes(array(
						'type' => $type,
//						'status'=> '1',//1-enabled, 2- disabled
					));
		}
        return $this;
    }

    public function processResponse($responseXML)
    {
        Mage::log('**ORDER MODEL START PROCESS RESONSE**', null, 'debug_orderformat.log');
        if(!$responseXML || $responseXML == ''){
            Mage::log('**ORDER MODEL END PROCESS RESONSE**1***', null, 'debug_orderformat.log');
            return false;
        }
        try {
            @$this->responseDOM = new SimpleXMLElement($responseXML);
            $this->responseFormatDOM = new SimpleXMLElement($this->getResponse());

            $this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);

			if(Mage::registry('number')) {
				Mage::unregister('number');
			}
        } catch (Exception $e) {
            Mage::log($e->getMessage(),null,'orders.log');
            Mage::log('**ORDER MODEL END PROCESS RESONSE**FALSE***', null, 'debug_orderformat.log');
            Mage::log($e->getMessage(), null, 'debug_orderformat.log');
            return false;
        }
        Mage::log('**ORDER MODEL END PROCESS RESONSE**TRYE***', null, 'debug_orderformat.log');
        return true;
    }

    public function processResponseByRequest($response, $responseFormat)
    {
        if (Mage::helper('cpcore')->isXML($response) && Mage::helper('cpcore')->isXML($responseFormat)){
            $this->responseDOM = new SimpleXMLElement($response);
            $this->responseFormatDOM = new SimpleXMLElement($responseFormat);
            return  $this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
        }
        return false;
    }

    public function getOrderCollection($formatId, $precondition = false) {
        if (!$precondition){
            $format = Mage::getModel('cpcore/xmlformat')
                ->setStoreId(0)
                ->loadByAttributes(array(
                'type' => Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getType('order'),
                'status' => '1', //1-enabled, 2- disabled
                'entity_id' => $formatId
            ));
            if ($format){
                $precondition = $format->getData('precondition');
            }
        }

        $statuses = explode(',', Mage::getStoreConfig('common/format/order_statuses'));

        $orders = Mage::getModel('sales/order')->getCollection()->addAttributeToFilter('status', $statuses);
        if ($precondition && $precondition != '') {
            $conditions = $statuses = explode('+', $precondition);
            foreach ($conditions as $condition) {
                preg_match_all("/(?P<name>[^-]*)-(?P<suf>[^-]*)-(?<values>.*)/i", trim($condition), $matches);
                if (($matches['name'][0] != '') && ($matches['suf'][0] != '') && ($matches['values'][0] != '')) {
                    $values = explode(',', $matches['values'][0]);
                    if (count($values) < 2) {
                        $values = $matches['values'][0];
                    }
                    $orders = $orders->addAttributeToFilter($matches['name'][0], array($matches['suf'][0] => $values));
                }
            }
        }
        return $orders;
    }

    public function getOrdersId($xmlformatId){
        $result = array();
        $orderCollection = $this->getOrderCollection($xmlformatId);
        foreach($orderCollection as $order){
            $result[] = $order->getId();
        }
        return $result;
    }

    public function divideCondition($format_id){
        $condition = Mage::getModel('cpcore/xmlformat')->load($format_id)->getCondition();
        if($condition and $condition != ''){
            $result = $condition;
            $result = str_replace('  ', '', $result);
            $result = str_replace('{{', '', $result);
            $result = str_replace('}}', '', $result);
            $result = str_replace('cond ', '', $result);
            $result = str_replace(' AND ', ';AND;', $result);
            $result = str_replace(' OR ', ';OR;', $result);
            $result = str_replace(' and ', ';and;', $result);
            $result = str_replace(' or ', ';or;', $result);
            $result = str_replace(' && ', ';&&;', $result);
            $result = str_replace(' || ', ';||;', $result);
            $result_array = explode(';', $result);
            return $result_array;
        }
        return false;
    }

    public function getOrders(){
        $store_id = Mage::app()->getStore()->getId();
        $orders = Mage::getModel('sales/order')->setStoreId($store_id)->getCollection();
        return $orders;
    }

    public function getConditionForOrder($condition, $order, $format_id){
        $this->setFormat($format_id);
        $this->addVariable('order', $order);

        $template = $this->getTemplate();
        $this->format->setOutXml($condition);
        $template->setXmlformat($this->format);
        $vars = $this->getVariables();

        $xml = $this->substitutionVarsToXml('', $template, $vars);

        if($xml)
        {
            return 'true';
        }
        return 'false';
    }

    public function getTotalConditionForOrder($condition, $order, $format_id){
        if($this->getConditionForOrder($condition, $order, $format_id) == 'true')
        {
            return 'performed';
        } else{
            return 'not performed';
        }
    }
}
