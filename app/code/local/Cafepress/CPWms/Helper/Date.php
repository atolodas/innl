<?php
class Cafepress_CPWms_Helper_Date extends Cafepress_CPWms_Helper_Data
{
    /**
     * Function for save shipping
     * Convert date to local date
     * @param type $date
     * @return type 
     */
    public function localizeDate($date) {
        $format = 'Y-m-d H:00:00';
        $result = Mage::getModel('core/date')->gmtDate($format,$date);
        return $result;
    }
}
