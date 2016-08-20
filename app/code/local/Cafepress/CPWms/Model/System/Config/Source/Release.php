<?php
class Cafepress_CPWms_Model_System_Config_Source_Release
{
    
    /**
    * identification method used to form xml
    */
    const RELEASE_1 = 0;
    const RELEASE_2 = 1;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
        	array('value' => self::RELEASE_1, 'label'=>Mage::helper('cpwms')->__('Release 1')),
            array('value' => self::RELEASE_2, 'label'=>Mage::helper('cpwms')->__('Release 2')),
        );
    }
    
    public function getRelease($release){
        switch ($release){
            case 'RELEASE_1': return self::RELEASE_1;
                break;
            case 'RELEASE_2': return self::RELEASE_2;
                break;
            default : return self::RELEASE_1;
        }
    }

}
