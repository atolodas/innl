<?php

class Cafepress_CPWms_Model_Resource_Eav_Mysql4_Xmlformat_Requestmethods extends Mage_Core_Model_Abstract
{
    static private $requestTypes = array(
            array(
                'id'    => '1',
                'name'  => 'ftp',
                'title' => 'FTP'
            ),
            array(
                'id'    => '2',
                'name'  => 'soap',
                'title' => 'SOAP'
            ),
            array(
                'id'    => '3',
                'name'  => 'http',
                'title' => 'HTTP'
            ),
            array(
                'id'    => '4',
                'name'  => 'simple_url',
                'title' => 'Simple URL'
            ),
            array(
                'id'    => '5',
                'name'  => 'get_file',
                'title' => 'Send File'
            )


        );


    /**
     * Retrieve option array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        $result = array();
        foreach (self::$requestTypes as $key=>$val){
            $result[$val['id']] = Mage::helper('cpwms')->__($val['title']);
        }
        return $result;
    }
    
    static public function getOptions()
    {
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
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
                'label' => Mage::helper('cpwms')->__('-- Please Select --')
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
        foreach (self::$requestTypes as $type){
            if ($type['name'] == $formatType){
                return $type['id'];
            }
        }
        return false;
    }
    
    public function getIdTypeByName($nameType)
    {
        foreach (self::$requestTypes as $type){
            if ($type['name'] == $nameType){
                return $type['id'];
            }
        }
        return false;
    }

    public function getNameTypeById($idType){
        foreach (self::$requestTypes as $type){
            if ($type['id'] == $idType){
                return $type['name'];
            }
        }
        return false;
    }
    
    public function getTitleTypeById($idType){
        foreach (self::$requestTypes as $type){
            if ($type['id'] == $idType){
                return Mage::helper('cpwms')->__($type['title']);
            }
        }
        return false;
    }
    
}