<?php
/**
 * Templates model
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Model_Templates extends Mage_Core_Model_Abstract
{
    /**
     * Init resource model
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('dcontent/templates');
    }

    public function toOptionArray(){
        $collection = $this->getCollection()->addOrder('header','asc');
        $option_array = array();
        foreach($collection as $webform)
            $option_array[]= array('value'=>$webform->getId(), 'label' => $webform->getHeader());
        return $option_array;
    }
}