<?php

class Cafepress_CPCore_Model_System_Config_Source_Ftpmethod {
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
            array('value' => self::FTP, 'label' => Mage::helper('cpcore')->__('FTP')),
            array('value' => self::FTPS, 'label' => Mage::helper('cpcore')->__('FTPS')),
        );
    }

}
