<?php

class Cafepress_CPWms_Model_Resource_Eav_Mysql4_Xmlformat_Schedule extends Mage_Core_Model_Abstract
{
    
    static private $scheduleFrequency = array(
            '0' => array(
                'name'  => 'none',
                'title' => 'none',
                'delay'  => 'none'
            ),
            '1' => array(
                'name'  => 'hour1',
                'title' => '1 Hour',
                'delay'  => '1 hour'
            ),
            '2' => array(
                'name'  => 'day1',
                'title' => '1 Day',
                'delay'  => '1 day'
            ),
            '3' =>array(
                'name'  => 'week1',
                'title' => '1 Week',
                'delay'  => '1 week'
            ),
            '4' =>array(
                'name'  => 'minute1',
                'title' => '1 Minute',
                'delay'  => '1 minute'
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
        foreach (self::$scheduleFrequency as $key=>$val){
            $result[$key] = $val['title'];
        }
        return $result;
    }
    
    static public function getOptions()
    {
        $res = array();
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
    
    public function getDelayById($id)
    {
        foreach (self::$scheduleFrequency as $key=>$val){
            if($key == $id){
                return $val['delay'];
            }
        }
        return false;
    }
    
}