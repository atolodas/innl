<?php

class Cafepress_CPCore_Model_Resource_Eav_Mysql4_Xmlformat_Responsemethods extends Mage_Core_Model_Abstract
{

    static private $responseTypes = array(
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
                'name'  => 'get_file',
                'title' => 'Send File'
            ),
            array(
                'id'    => '5',
                'name'  => 'save_to_session',
                'title' => 'Save To Session'
            ),
            array(
                'id'    => '6',
                'name'  => 'return_result',
                'title' => 'Parse response as XML'
            ),
                    array(
                'id'    => '7',
                'name'  => 'return_result_simple',
                'title' => 'Return Result As Is'
            ),
            array(
                'id'    => '8',
                'name'  => 'edit_result_return_all',
                'title' => 'Edit Result and Return All'
            ),
            array(
                'id'    => '9',
                'name'  => 'return_data',
                'title' => 'Return Data'
            ),
	    array(
                'id'    => '10',
                'name'  => 'mashape',
                'title' => 'Mashape APIs response (HTTPREsponse -> JSON -> XML and parse it)'
           ),
            array(
                'id'    => '11',
                'name'  => 'json',
                'title' => 'JSON 2 XML and parse it'
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
        foreach (self::$responseTypes as $key=>$val){
            $result[$val['id']] = Mage::helper('cpcore')->__($val['title']);
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
        foreach (self::$responseTypes as $type){
            if ($type['name'] == $formatType){
                return $type['id'];
            }
        }
        return false;
    }

    public function getIdTypeByName($nameType)
    {
        foreach (self::$responseTypes as $type){
            if ($type['name'] == $nameType){
                return $type['id'];
            }
        }
        return false;
    }

    public function getNameTypeById($idType){
        foreach (self::$responseTypes as $type){
            if ($type['id'] == $idType){
                return $type['name'];
            }
        }
        return false;
    }

    public function getTitleTypeById($idType){
        foreach (self::$responseTypes as $type){
            if ($type['id'] == $idType){
                return Mage::helper('cpcore')->__($type['title']);
            }
        }
        return false;
    }

}
