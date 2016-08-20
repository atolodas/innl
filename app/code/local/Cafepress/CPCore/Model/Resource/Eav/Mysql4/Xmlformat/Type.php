<?php

class Cafepress_CPCore_Model_Resource_Eav_Mysql4_Xmlformat_Type extends Mage_Core_Model_Abstract
{
    const TYPE_ORDER    = 1;
    const TYPE_CREDITMEMO   = 2;
    
    static private $formatTypes = array(
            /* array(
                'id'    => '1',
                'name'  => 'order',
                'title' => 'Order',
                'develop'   => false
            ),
            array(
                'id'    => '2',
                'name'  => 'creditmemo',
                'title' => 'Credit Memo',
                'develop'   => false
            ),
            array(
                'id'    => '3',
                'name'  => 'orderstatus',
                'title' => 'Orderstatus',
                'develop'   => false
            ), */
            array(
                'id'    => '4',
                'name'  => 'downfileparsresp',
                'title' => 'Download File and Parse Response',
                'develop'   => false
            ), 
            array(
                'id'    => '5',
                'name'  => 'transformer',
                'title' => 'Transformer',
                'develop'   => true
            ),

        );
    

        /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        $result = array();
        foreach (self::$formatTypes as $key=>$val){
            $result[$val['id']] = Mage::helper('cpcore')->__($val['title']);
        }
        return $result;
    }
    
    static public function getOptions()
    {
        $result = array();
        $developer = Mage::helper('cpcore')->isDeveloper();
        foreach (self::$formatTypes as $type){
            if ($type['develop']){
                if ($developer == $type['develop']){
                    $result[$type['id']] = Mage::helper('cpcore')->__($type['title']);
                }
            } else {
                $result[$type['id']] = Mage::helper('cpcore')->__($type['title']);
            }
        }
        return $result;
    }
    
    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    public function getAllOptions()
    {
        $res = array(
            array(
                'value' => '',
                'label' => Mage::helper('cpcore')->__('-- Please Select --')
            )
        );
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }
    
    /**
     *
     * @param type $formatType
     * @return type 
     */
    public function getType($formatType)
    {
        foreach (self::$formatTypes as $type){
            if ($type['name'] == $formatType){
                return $type['id'];
            }
        }
        return false;
    }
    
    public function getIdTypeByName($nameType)
    {
        foreach (self::$formatTypes as $type){
            if ($type['name'] == $nameType){
                return $type['id'];
            }
        }
        return false;
    }

    public function getNameTypeById($idType){
        foreach (self::$formatTypes as $type){
            if ($type['id'] == $idType){
                return $type['name'];
            }
        }
        return false;
    }
    
    public function getTitleTypeById($idType){
        foreach (self::$formatTypes as $type){
            if ($type['id'] == $idType){
                return Mage::helper('cpcore')->__($type['title']);
            }
        }
        return false;
    }
    
    public function getDeveloperTypeIds()
    {
        $result = array();
        foreach (self::$formatTypes as $type){
            if ($type['develop']){
                $result[] = $type['id'];
            }
        } 
        return $result;
    }
    
}
