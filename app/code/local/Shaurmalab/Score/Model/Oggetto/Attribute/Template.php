<?php
class Shaurmalab_Score_Model_Oggetto_Attribute_Template extends Mage_Core_Model_Abstract
{
    /**
     * Init resource model
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('dcontent/dcontent');
    }



    public function toOptionArray(){

        $option_array = array();
        $option_array[]= array('value'=>'score/oggetto/attribute/edit.phtml', 'label' => 'default');
        $option_array[]= array('value'=>'score/oggetto/attribute/edit-advanced.phtml', 'label' => 'advanced');

        return $option_array;
    }
}
