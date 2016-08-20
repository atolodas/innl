<?php

abstract class Cafepress_CPCore_Model_Xmlformat_Format_Abstract extends Cafepress_CPCore_Model_Abstract
{
    const XML_ACTION    = 'wms_action';
    const XML_REPEAT    = 'wms_repeat';

    const XML_IF        = 'wms_if';
    const XML_TERM      = 'wms_term';
    const XML_IF_TRUE   = 'wms_action';//'wms_if_true';
    const XML_IF_FALSE  = 'wms_else';//'wms_if_else';
    const XML_ELSE      = 'wms_else';
//    const XML_ATTRIBUTE      = 'wms_attributes';

    protected   $variables          = array();
    protected   $responseDOM        = false;
    protected   $responseFormatDOM  = false;

    protected $_escapeChars     = array(
                                        '&#11;'     => 'aMp!!!!!!!!?!',
                                        '&'         => 'aMp2!!!!!!!!?!',
                                        "\n"        => "!n!",
                                        "\r"        => "!r!",
                                        "#"         => "!?!?!?!?!",
                                        "%"         => "????!!!!"
                                    );

    protected $_escapeChars2    = array(
        "\n" => "",
        "\r" => ""
    );

    protected   $_childName     = false;

    /**
     *
     * @var type stdObj
     */
    protected $remoteFormatData = null;

    public function _construct() {
        $this->_init('cpcore/xmlformat');
        parent::_construct();
    }


    protected function substitutionVarsToXml($xml, $template,  $vars = array())
    {
//        $vars = $this->getVariables();
        Varien_Profiler::start("xmlformat_template_proccessing");
        $templateProcessed = $template->getProcessedTemplate($vars, true);
        Varien_Profiler::stop("xmlformat_template_proccessing");
        $xml .= $templateProcessed;
        return $xml;
    }

    /**
     *
     * @param type $object - object from wms-format xml
     * @param type $resource - object O from import xml
     * @param type $nameObj   - name O
     * @param type $resourceUp - parent O
     * @return type
     */
    protected function runXmlElementActions($object, $resource = false, $nameObj=false,
            $resourceUp = false, $resourceUpName = false, $pathName = array())
    {
        $this->_childName = $resourceUpName;
        if ($object->getName() && ($object->getName()!='')){
            $pathName[] = $object->getName();
        }
        if ($this->isAction($nameObj)) {
            $attributes = $this->getAttributes($resourceUp);
            $this->_getModelByCode('var')->setVar('attributes',$attributes);
            $this->_getModelByCode('var')->setVar('wms_block_name',$resourceUpName);
            $this->_getModelByCode('var')->setVar('wms_path_name',$pathName);

            $this->runAction($object, $resourceUp);
            return;
        } elseif ($massIf = $this->isIf($nameObj,$object)){
//            $massIf = $this->isIf($nameObj,$object);
            $attributes = $this->getAttributes($resourceUp);
            $this->_getModelByCode('var')->setVar('attributes',$attributes);
            $this->_getModelByCode('var')->setVar('wms_block_name',$resourceUpName);
            $this->_getModelByCode('var')->setVar('wms_path_name',$pathName);
            $termS = (string)$massIf['term'];

            preg_match_all("/{{([a-zA-Z]+) (.*?)}}/", $termS, $termMathes, PREG_SET_ORDER);

            $terms = array('search'=>array(),'replace'=>array());
            foreach ($termMathes as $term) {
                $terms['search'][]  = $term[0];
                $terms['replace'][] = (int)(bool)$this->runAction($term[0], $resourceUp);
            }
            $termS = str_replace($terms['search'], $terms['replace'], $termS);
            if ($termS && (strlen($termS)>1)){
                eval('$termS = (boolean)('.$termS.');');
            }

            if ($termS){
                $this->runAction($massIf['action'], $resourceUp, false);
            } else {
                if ($massIf['else']!=''){
                    $this->runAction($massIf['else'], $resourceUp, false);
                }
            }
            return;
        } elseif ($this->isRepeat($object)){
            $count = 0;
            if (count($resourceUp->$nameObj)>0){
                foreach($resourceUp->$nameObj as $resKey=>$resVal){
                    foreach ($object as $key => $val){
                        $this->runXmlElementActions($val, $resource[$count]->$key, $key, $resVal, $nameObj);
                    }
                    $count++;
                }
            }

        } else {
            if (count($object) > 0){
                foreach ($object as $key => $val){
                    if (is_object($resource)){
                        $this->runXmlElementActions($val, $resource->$key, $key, $resource, $nameObj, $pathName);
                    }
                }
            }
        }
    }

    /**
     *
     * @param type $element
     * @return type
     */
    private function hasAction($element){
        $tag = self::XML_ACTION;
        if ((count($element->$tag)>0) && (count($element->$tag->$tag)<=0)){
            $actions = array();
            foreach($element as $key=>$val){
                $actions[] = $val;
            }
            return $actions;
        }
        return false;
    }

    private function isAction($key){
        $tag = self::XML_ACTION;
        if ($key == $tag){
            return true;
        }
        return false;
    }

    /**
     *
     * @param type $element
     * @return type
     */
    private function getAttributes($element){
        $result = array();
        if ($element[0]){
            $attributes = (array)$element[0]->attributes();
            if (isset($attributes['@attributes'])){
                $result = $attributes['@attributes'];
            }

        }

        return $result;
    }

    /**
     *
     * @param type $element
     * @return type
     */
    private function isRepeat($element){
        $tag = self::XML_REPEAT;
        if ($element[$tag]==true){
            return true;
        }
        return false;
    }

    private function isIf($key, $element)
    {
        if ($key =! self::XML_IF){
            return false;
        }

        $tagTerm = self::XML_TERM;
        $tagAction = self::XML_IF_TRUE;
        $tagElse = self::XML_IF_FALSE;

        if ((count($element->$tagTerm)>0)){
            $term = $element->$tagTerm;
            $action = $element->$tagAction;
            $else = $element->$tagElse;
            $result = array(
                'term'  => $term,
                'action'  => $action,
                'else'  => $else,
            );
            return $result;
        }
        return false;

    }

    protected function runAction($actionStr, $value, $saveValue = true)
    {

        if ((strpos($actionStr, '[[')!=false)&&(strpos($actionStr, ']]')!=false)){
            preg_match_all("/\[\[([^]]*)]]/i", $actionStr, $matches);
            foreach($matches[1] as $comand){
                $result = $this->runAction('{{'.$comand.'}}', $value);

                $uniqName = uniqid();
                $this->_getModelByCode('var')->setVar($uniqName,$result);
                $actionStr = str_replace('[['.$comand.']]','{'.$uniqName.'}',$actionStr);
            }
        }

        if (($value!=NULL) && ($value instanceof SimpleXMLElement)){
            $strXML = $value->asXML();
            $strXML = str_replace('<'.$this->_childName.'>', '', $strXML);
            $value = substr($strXML,0,  strlen($strXML) - strlen('</'.$this->_childName.'>'));
        }  elseif($value!=NULL) {
            $value = trim($value);
            $strXML = $value;
        } else {
            $strXML = '';
        }
        $strXMLall = $strXML;

        $value =  html_entity_decode($this->unRepleaseCharsXML((string)$value));
        if ($saveValue!=false){
             $this->_getModelByCode('var')->setVar('value',$value)->setVar('val',$value);
        }
        $this->_getModelByCode('var')->setVar('all_block_content',$strXMLall);

        $result = null;
        $actions = array();
        preg_match_all("/{{([a-zA-Z]+) (.*?)}}/", $actionStr, $commands, PREG_SET_ORDER);
        foreach($commands as $command){
            $entityName = $command[1];
            $actionsStr = $command[2];
            preg_match_all("/([a-zA-Z]+)\(([^)]*)\)/", $actionsStr, $actions, PREG_SET_ORDER);

            if ((''!=$entityName)&&(count($actions)>0)){
                $entity = $this->_getModelByCode($entityName);
                foreach($actions as $action){
                    $act = $action[1];
                    $variablesStr = $action[2];
                    $values = array();
                    /*variables*/
                    if ($variablesStr!=''){
                        $variables = explode(',', $variablesStr);
                        array_walk($variables, create_function('&$val', '$val = trim($val);'));
                        $values = $variables;
                        foreach($values as $key=>$val){
                            $matches = array();
                            if (preg_match("/{([^}]*)}/i", $val, $matches)){
                                $variables[$key] = $this->_getModelByCode('var')->getVar($matches[1]);
                            }
                        }
                        $values = $variables;
                    }
                    $values = $this->convertValuesFromFunction($values);

                    switch (count($values)) {
                        case 0:{
                            $result = $entity->$act();
                        }break;
                        case 1:{
                            $result = $entity->$act($values[0]);
                        }break;
                        case 2:{
                            $result = $entity->$act($values[0], $values[1]);
                        }break;
                        case 3:{
                            $result = $entity->$act($values[0], $values[1], $values[2]);
                        }break;
                        default:
                            Mage::log("WMS has Error: function $act() has more than 3 variables. Please request about Serg.)",null,'wms.log');
                            die(__METHOD__.'Fatal Error during process WMS Format');
                            break;
                    }
                }
            }
        }
        return $result;
    }

    protected function convertValuesFromFunction($data = array()){
        $result = array();
        foreach ($data as $val){
            switch ($val){
                case 'null': $val = null;
                    break;
                case 'true': $val = true;
                    break;
                case 'false': $val = false;
                    break;
//                default : {
//
//                }
//                    break;
            }
            $result[] = $val;
        }
        return $result;
    }

    protected function _getModelByCode($modelCod)
    {
        $entitiesPathes = $this->getEntityPathes();
        if (isset($entitiesPathes[$modelCod])){
            return Mage::getSingleton($entitiesPathes[$modelCod]);
        }
        return false;
    }

    protected function getEntityPathes(){
        $cacheId = 'CPCORE_CONFIG_ENTITESPATH';
        $cacheData = Mage::app()->getCache()->load($cacheId);
        if ($cacheData !== false) {
            $result = unserialize($cacheData);
        } else {
            $entities = Mage::getConfig()->getNode('entities', 'cpcore');
            $result = array();
            foreach ((array) $entities as $key => $value) {
                $result[$key] = $value;
            }
            if (count($result) == 0) {
                die('Please set entities config for WMS in config.xml');
            }
            Mage::app()->getCache()->save(serialize($result), $cacheId, array('CPCORE'));
        }
        return $result;
    }

    public function getVariables()
    {
        return $this->variables;
    }

    protected function getTemplate()
    {
        return Mage::getModel('cpcore/template')->setStoreId($this->getStoreId());
    }

    public function addVariables($variables = array())
    {
        $this->variables += $variables;
        return $this;
    }

    public function addVariable($key, $value)
    {
        $this->variables[$key] = $value;
        return $this;
    }

    /**
     *Copy $this->addVariable($key, $value)
     * only has a short name :)
     * @param type $key
     * @param type $value
     * @return type
     */
    public function addVar($key, $value)
    {
        $this->_getModelByCode('var')->setVar($key,$value);
        return true;
    }

    protected function getResponseFormat()
    {
        return $this->getResponse();
    }


    public function processResponse($responseXML)
    {
        $responseXML = $this->repleaseCharsXML2($responseXML);
        try {
            $this->responseDOM = new SimpleXMLElement($responseXML);
            $this->responseFormatDOM = new SimpleXMLElement($this->getResponseFormat());

            $this->runXmlElementActions($this->responseFormatDOM, $this->responseDOM);
        } catch (Exception $e) {
            Mage::log($e->getMessage(),null,'wms.log');
            return false;
        }
        return true;
    }

    public function repleaseCharsXML($xml)
    {
        $chars = array();
        $code = array();
        $i = 0;
        $xml = trim($xml);
        foreach($this->_escapeChars as $key => $val){
            $chars[$i]  = $key;
            $code[$i]   = $val;
            $i++;
        }
        $xml = str_replace($chars,$code,$xml);
        return $xml;
    }

    public function repleaseCharsXML2($xml)
    {
        $chars = array();
        $code = array();
        $i = 0;
        $xml = trim($xml);
        foreach($this->_escapeChars2 as $key => $val){
            $chars[$i]  = $key;
            $code[$i]   = $val;
            $i++;
        }
        $xml = str_replace($chars,$code,$xml);
        return $xml;
    }

    public function unRepleaseCharsXML($xml)
    {
        $chars = array();
        $code = array();
        $i = 0;
        foreach($this->_escapeChars as $key => $val){
            $chars[$i]  = $key;
            $code[$i]   = $val;
            $i++;
        }
        $xml = str_replace($code,$chars,$xml);
        $xml = html_entity_decode($xml, ENT_QUOTES, 'UTF-8');
        return $xml;
    }

    public function echoMessage($message)
    {
        echo $message;
    }

    /**
     * This method should be overridden in children classes
     * @return type
     */
    public function getUrlOfRequest()
    {
        $result = array();

        $methods = $this->getMethodOfRequest();
        foreach ($methods as $method){
            switch ($method){
                case 'sentFileByFtp':{
                    $server = trim(Mage::getStoreConfig('ftp/orders/address', $this->getStoreId()));
                    $folder = trim(Mage::getStoreConfig('ftp/orders/inbound', $this->getStoreId()));
                    $result['sentFileByFtp'] = "ftp://$server/$folder/";
                } break;
                case 'loadFileByFtp':{
                    $server = trim(Mage::getStoreConfig('ftp/orders/address', $this->getStoreId()));
                    $folder = trim(Mage::getStoreConfig('ftp/orders/outbound', $this->getStoreId()));
                    $result['loadFileByFtp'] = "ftp://$server/$folder/";
                } break;
                case 'sentFileByHttp':{
                    if ($this->_getCustomUrl()){
                        $result['sentFileByHttp'] = $this->_getCustomUrl();
                    } else {
                        $result['sentFileByHttp'] = Mage::getStoreConfig('http/orderstatus/server', $this->getStoreId());
                    }
                } break;
                case 'sentFileBySoap':{
                    if ($this->_getCustomUrl()){
                        $result['sentFileBySoap'] = $this->_getCustomUrl();
                    } else {
                        $result['sentFileBySoap'] = Mage::getStoreConfig('soap/options/server', $this->getStoreId());
                    }

                } break;
            }
        }
        return $result;

    }

     /**
     * This method should be overridden in children classes
     * @return type
     */
    public function getMethodOfRequest()
    {
        $useMethods = Mage::getStoreConfig('common/format/outbound_methods', $this->getStoreId());
        $methodsAr = explode(',', trim($useMethods));
        return $methodsAr;
    }

    public function _getCustomUrl()
    {
        return $this->getCustomUrl();
    }

    protected function _getResponse()
    {
        $response = $this->getResponse();
        if (!Mage::helper('cpcore')->isXML($response)){
            Mage::log('Error XML in format:'.$this->getName(),null,'wms.log');
            return '';
        }
        return $response;
    }

    public function setRemoteFormatData($data){
        $this->remoteFormatData = $data;
        return $this;
    }

    public function getRemoteFormatData($key=false){
        if ($key === false){
            return $this->remoteFormatData;
        }
        if (isset($this->remoteFormatData->$key)){
            return $this->remoteFormatData->$key;
        }
    }

    public function getRemoteFormatDataAdditional($key=false){
        $result = null;
        try{
            if (isset($this->remoteFormatData->additional)){
                $additionalData = unserialize($this->remoteFormatData->additional);
                if(!$key){
                    $result = $additionalData;
                } else {
                    if (isset($additionalData[$key])){
                        $result = $additionalData[$key];
                    }
                }
            }

        } catch (Exception $e) {
            Mage::log($e->getMessage(),null,'cafepress.log');
        }
        return $result;
    }


    public function isUseApi(){
        return (bool)$this->remoteFormatData;
    }



}
