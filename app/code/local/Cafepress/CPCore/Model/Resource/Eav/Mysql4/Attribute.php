<?php

class Cafepress_CPCore_Model_Resource_Eav_Mysql4_Attribute extends Mage_Eav_Model_Mysql4_Entity_Attribute
{
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $applyTo = $object->getApplyTo();
        if (is_array($applyTo)) {
            $object->setApplyTo(implode(',', $applyTo));
        }
        return parent::_beforeSave($object);
    }


}
