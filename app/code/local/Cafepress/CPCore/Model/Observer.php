<?php

abstract class Cafepress_CPCore_Model_Observer extends Mage_Core_Model_Abstract
{
    public function editWmsHttpCurlSetopt($event){
        $ch = $event->getCurl();
    }
}
