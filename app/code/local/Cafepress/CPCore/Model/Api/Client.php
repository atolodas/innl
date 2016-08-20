<?php

class Cafepress_CPCore_Model_Api_Client extends Varien_Object
{
    /**
     *
     * @var type SoapClient
     */
    protected $soapClient   = null;

    /**
     *
     * @var type string
     */
    protected $sessionId    = null;
    
    
    public function getSoapClient(){
        if (!$this->soapClient){
            try {
                $soapClient = new SoapClient(Mage::getStoreConfig('wms_client/partner/serverurl'));
            } catch (SoapFault $e) {
                Mage::log('WMS API:'.$e->getMessage(), null, 'cafepress.log');
                return false;
            }
            $this->soapClient = $soapClient;
        }
        return $this->soapClient;
    }
    
    public function getSessionId(){
        if (!$this->sessionId){
            try {
                $soapClient = $this->getSoapClient();
                $sessionId = $soapClient->login(
                        Mage::getStoreConfig('wms_client/partner/apiuser'), 
                        Mage::getStoreConfig('wms_client/partner/apikey')
                );
            } catch (SoapFault $e) {
                Mage::log('WMS API:'.$e->getMessage(), null, 'cafepress.log');
                return false;
            }
            $this->sessionId = $sessionId;
        }
        return $this->sessionId;
    }
}
