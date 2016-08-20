<?php
class Shaurmalab_Score_Model_Oggetto_Attribute_Fieldtype extends Mage_Core_Model_Abstract
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
        $option_array[]= array('value'=>'', 'label' => 'Not Selected');
        $option_array[]= array('value'=>'image', 'label' => 'image');
        $option_array[]= array('value'=>'checkbox', 'label' => 'checkbox');
        $option_array[]= array('value'=>'radio', 'label' => 'radio');
        $option_array[]= array('value'=>'switcher', 'label' => 'switcher');
        $option_array[]= array('value'=>'text', 'label' => 'text');
         $option_array[]= array('value'=>'inverse', 'label' => 'inverse');
        return $option_array;
    }
}
