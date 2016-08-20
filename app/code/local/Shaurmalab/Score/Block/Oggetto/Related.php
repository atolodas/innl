<?php
class Shaurmalab_Score_Block_Oggetto_Related extends Shaurmalab_Score_Block_Oggetto_Abstract
{

    public  function  _toHtml() {
        $html = '';

        $id = Mage::helper('score/oggetto')->getSetIdByCode($this->getSet());
        $oggettos = Mage::getModel('score/oggetto')->getAvailableObjects($id)
        ->addAttributeToFilter($this->getAttribute(),Mage::helper('score')->getLikeArray($this->getAttribute(),$this->getId()));
       // $html = $oggettos->getSelect();
        $options = array();
        foreach($oggettos as $oggetto) {
            $options[] = $oggetto->getData($this->getReturn());
        }
        if(count($options)) {
            $html = implode(',',$options);
        } else {
            $html = 'N/A';
        }
        return $html;
    }

    public function haveRelated($set,$attribute,$oid) {

        $id = Mage::helper('score/oggetto')->getSetIdByCode($set);
        $oggettos = Mage::getModel('score/oggetto')->getAvailableObjects($id)
            ->addAttributeToFilter($attribute,Mage::helper('score')->getLikeArray($attribute,$oid));
        // $html = $oggettos->getSelect();
       // echo $oggettos->getSelect();die;
        $options = array();
        foreach($oggettos as $oggetto) {
            $options[] = $oggetto->getId();
        }
        if(count($options)) return true;
        return false;

    }
}