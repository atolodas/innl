<?php

abstract class Cafepress_CPWms_Model_Cafepress extends Mage_Core_Model_Abstract
{
    public function editWmsHttpCurlSetopt($event){
        $ch = $event->getCurl();
    }
}
