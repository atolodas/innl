<?php

class Cafepress_CPWms_Model_System_Config_Source_Ftpmethod {
    /**
     * identification method used to form xml
     */
    const FTP = 'ftp';
    const FTPS = 'ftps';

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray() {
        return array(
            array('value' => self::FTP, 'label' => Mage::helper('cpwms')->__('FTP')),
            array('value' => self::FTPS, 'label' => Mage::helper('cpwms')->__('FTPS')),
        );
    }

}
