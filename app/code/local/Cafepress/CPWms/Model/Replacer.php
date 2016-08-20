<?php

class Cafepress_CPWms_Model_Replacer extends Mage_Core_Model_Abstract
{
    protected $_allReplace = false;
    protected $_filterModel = false;
    protected $_lineTypes = false;

    protected function _construct() {
        $this->_init('wmsreplacer/replacer');
    }

    protected function _getValuesModel(){
        return Mage::getSingleton('wmsreplacer/replacer_sub');
    }
    
    protected function _getLineModel(){
        return Mage::getSingleton('wmsreplacer/replacer_sub');
    }
    
    public function _getLineZModule(){
        return Mage::getSingleton('cpwms/replacer_line');
    }
    
    public function getLineCodeIdById($code){
        if (!$this->_lineTypes){
            $this->_lineTypes = Mage::helper('cpwms/replacer')->getLineTypes();
        }
        foreach ($this->_lineTypes as $type) {
            if ($type['name']==$code){
                return $type['id'];
            }
        }
        return false;
    }
    
    public function getTypeCodeById($id){
        if (!$this->_lineTypes){
            $this->_lineTypes = Mage::helper('cpwms/replacer')->getLineTypes();
        }
        foreach ($this->_lineTypes as $type) {
            if ($type['id']==$id){
                return $type['name'];
            }
        }
        return false;
    }

    public function isDeleteable()
    {
        if (!$this->getId()){
            return false;
        }
        return true;
    }

    public function setValues($values){
        $this->_getValuesModel()
            ->setReplacerId($this->getId())
//            ->setReplacer($this)
            ->setValues($values);
    }

    public function  delete(){
        $this->_getValuesModel()->deleteLineByReplacer($this->getId());
        parent::delete();
    }

    public function _getAllReplace(){
        if (!$this->_allReplace){
            $allReplace = array();
            $collection = $this->getCollection();
            foreach($collection as $replacer){
//                $allReplace[$replacer->getId()]['pattern'] = $replacer->getPattern();
                $allReplace[$replacer->getId()] = $replacer->getData();
            }
            $collectionSub = $this->_getValuesModel()->getCollection();

            foreach($collectionSub as $sub){
                if ($sub->getStoreId() == 0){
                    $line = $this->_getLineZModule()->loadByLineId($sub->getLineId());
                    $allReplace[$sub->getReplacerId()]['line'][$sub->getLineId()]['default'] =  $sub->getValue();
                    $allReplace[$sub->getReplacerId()]['line'][$sub->getLineId()]['type'] =  $line->getType();
                } else {
                    $allReplace[$sub->getReplacerId()]['line'][$sub->getLineId()]['sub'][$sub->getStoreId()] =  $sub->getValue();
                }
            }
            $this->_allReplace = $allReplace;
        }
        return $this->_allReplace;
    }

    public function existReplaceConstruction($construction,$default,$storeId){
        $allReplace = $this->_getAllReplace();
        foreach($allReplace as $replace){
            if ($replace["pattern"]==$construction){
//            if (preg_match($replace["pattern"],$construction)){
                foreach($replace['line'] as $line)
//                if (preg_match($default,$line["default"])){
                if ($line["default"]==$default){
                    return $line["sub"][$storeId];
                }
            }
        }
        return false;
    }
    
    public function existReplaceHelper($helper,$default,$storeId){
        $result = false;
        $allReplace = $this->_getAllReplace();
        foreach($allReplace as $replace){
            if ($replace["helper"]==$helper){
                foreach($replace['line'] as $line){
                    if (('disable'!=$this->getTypeCodeById($line["type"])) 
                            && (isset($line["sub"][$storeId]) && ($line["sub"][$storeId]!=''))){
                        if ((($this->getLineCodeIdById('constant')==$line["type"])&&($line["default"]==$default))
                            || (($this->getLineCodeIdById('match')==$line["type"])&&(preg_match($line["default"],$default)))){
                            if ($replace["conditions"]!=''){
                                $filter = $this->getFilterModel();
                                if($filter->filter($replace["conditions"])){
                                    $result =  $line["sub"][$storeId];
                                }
                            } else {
                                $result =  $line["sub"][$storeId];
                            }
                        }
                    } 
                }
            }
        }
        return $result;
    }

    public function replaceValue($construction,$replacedValue,$storeId){
        $replace = $this->existReplaceConstruction($construction,$replacedValue,$storeId);
        if ($replace){
            if ($replace!=NULL){
                $replacedValue = $replace;
            }
        }
        return $replacedValue;
    }
    
    public function replaceValueByHelper($helper,$replacedValue,$storeId){
        $replace = $this->existReplaceHelper($helper,$replacedValue,$storeId);
        if ($replace){
            if ($replace!=NULL){
                $replacedValue = $replace;
            }
        }
        return $replacedValue;
    }
    
    public function setFilterModel($filterModel){
        $this->_filterModel = $filterModel;
        return $this;
    }
    
    public function getFilterModel(){
        return $this->_filterModel;
    }
    
    public function getPossibleValues($construction){
//        $observer = Mage::getSingleton('cpwms/order_observer');
        $orders = Mage::getModel('sales/order')->getCollection()
//                ->addAttributeToFilter('entity_id', '1')
            ;
        $xmlformat = Mage::getModel('cpwms/xmlformat')->setStoreId(0);
        
        $result = array();
        foreach($orders as $order){
//            $value = $observer->getXmlByFormat($order, 'order', false, $formatId = 0);
            $result[] = $this->getXmlFormat($order, $xmlformat, $construction);
        }
        
        return array_unique($result);
    }
    
    public function getXmlFormat($order, $xmlformat, $construction){
        $payment = $order->getPayment();

        $storeId = $order->getStoreId(); //@todo: storeId ?
        $store = Mage::getModel('cpwms/xmlformat')->setStoreId($storeId)->getStore();
        
        $template = Mage::getModel('cpwms/template')->setStoreId($storeId);

        $xmlformat->setOutXml($construction);
        $template->setXmlformat($xmlformat);

        $xml = $this->substitutionVarsToXml(
                $template, 
                array(
                    'order' => $order,
                    'store' => $store,
                    'payment' => $payment,
                ));
        return $xml;
    }

    protected function substitutionVarsToXml($template, $vars = array()) {
        Varien_Profiler::start("xmlformat_template_proccessing_possible");
        $templateProcessed = $template->getProcessedTemplate($vars, true);
        Varien_Profiler::stop("xmlformat_template_proccessing_possible");
        return $templateProcessed;
    }


}
