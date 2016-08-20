<?php

class Cafepress_CPCore_Model_Xmlformat extends Cafepress_CPCore_Model_Abstract
{
    /**
     * Entity code.
     * Can be used as part of method name for entity processing
     */
    const ENTITY                = 'cpcore_xmlformat';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix     = 'cpcore_xmlformat';

    /**
     * Name of XMLFormat
     * @var type string
     */
    protected $formatName       = null;

    protected static $_types = array(
        'order' => array(
            'model' => 'cpcore/xmlformat_format_order'
        ),
        'creditmemo' => array(
            'model' => 'cpcore/xmlformat_format_order'
        ),
        'orderstatus' => array(
            'model' => 'cpcore/xmlformat_format_orderstatus'
        ),
        'file' => array(
            'model' => 'cpcore/xmlformat_format_downfileparsresp'
        ),
        'transformer' => array(
            'model' => 'cpcore/xmlformat_format_transformer'
        ),
        'oggetto' => array(
            'model' => 'score/oggetto'
        )
    );


    protected function _construct()
    {
        $this->_init('cpcore/xmlformat');
        parent::_construct();
    }

    public function validate()
    {
        Mage::dispatchEvent($this->_eventPrefix.'_validate_before', array($this->_eventObject=>$this));
        $this->_getResource()->validate($this);
        Mage::dispatchEvent($this->_eventPrefix.'_validate_after', array($this->_eventObject=>$this));
        return $this;
    }

    public function getAllFormatName()
    {
        $result = array();
        foreach($this->getCollection()->addAttributeToSelect('*') as $format){
            $result[$format->getId()] = $format->getName();
        }
        return $result;
    }

    /*
     * Api functional
     */

    public function setFormatName($formatName){
        $this->formatName = $formatName;
        return $this;

    }

    static public function getTypes(){
        return self::$_types;
    }


    public function getModelFormatByApi($formatName, $singleton=false){
        $this->setFormatName($formatName);
        $this->setUseApi();
        $config = $this->getFormatConfig($formatName);
        $types = self::getTypes();
        if(!$config){
            #TODO INL: Add adequat exseption
            die('ERROR API FORMAT1:'.$formatName);
            return false;
        }
        $remoteFormat = $this->getRemoteFormat($config['name']);

        if(!$remoteFormat){
            #TODO INL: Add adequat exseption
            die('ERROR API FORMAT2:'.$formatName);
            return false;
        }

        if ($singleton){
            $model = Mage::getSingleton($types[$config['type']]['model']);
            $model->setRemoteFormatData($remoteFormat);
        } else {
            $model = Mage::getModel($types[$config['type']]['model']);
            $model->setRemoteFormatData($remoteFormat);
        }
        return $model;
    }

    public function getRemoteFormat($formatName){
        $cacheId = 'CPCORE_WMS_REMOTE_FORMAT_'.$formatName;
        $cacheData = Mage::app()->getCache()->load($cacheId);
        if ($cacheData != false) {
            $remoteFormat = unserialize($cacheData);
        } else {
            try {
                $soapClient = Mage::getModel('cpcore/api_client')->getSoapClient();
                $sessionId  = Mage::getModel('cpcore/api_client')->getSessionId();
                if(!$sessionId){
                    return false;
                }
                $remoteFormat = $soapClient->wmsFormatGet($sessionId, $formatName);
            } catch (SoapFault $e) {
                Mage::log('WMS REMOTE FORMAT:'.$e->getMessage(), null, 'cafepress.log');
                return false;
            }
            Mage::app()->getCache()->save(serialize($remoteFormat), $cacheId, array('CPCORE'));
        }
        return $remoteFormat;
    }


    public function getFormatConfig($formatName){
        $cacheId = 'CPCORE_CONFIG_FORMATDATA';
        $cacheData = Mage::app()->getCache()->load($cacheId);
        if ($cacheData != false) {
            $result = unserialize($cacheData);
        } else {
            $formats = Mage::getConfig()->getNode('formats', 'cpcore');
            $result = array();

            foreach ((array) $formats as $key => $value) {
                $result[$key] = (array)$value;
            }
            Mage::app()->getCache()->save(serialize($result), $cacheId, array('CPCORE'));
        }
        if (!isset($result[$formatName])){
            Mage::log('WMS FORMAT CONFIG: No config by name "'.$formatName.'"', null, 'cafepress.log');
            return false;
        }
        return $result[$formatName];
    }

}
