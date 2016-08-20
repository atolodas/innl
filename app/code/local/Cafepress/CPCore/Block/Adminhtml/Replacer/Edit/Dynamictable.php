<?php

class Cafepress_CPCore_Block_Adminhtml_Replacer_Edit_Dynamictable extends Mage_Adminhtml_Block_Widget
{

    protected $_lines = false;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('cpcore/replacer/edit/dynamictable.phtml');
    }

    public function getReplacer()
    {
        return Mage::registry('current_replacer');
    }

    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getStores(){
        return Mage::helper('cpcore/replacer')->getStores();
    }

    public function getNewLineContent($lineId,$replaceStr){
        $newLineContent = $this->getLayout()->createBlock('cpcore/adminhtml_replacer_edit_dynamictable_newline')->toHtml();
        $result = str_replace($replaceStr,$lineId,$newLineContent);

        return $result;
    }

    public function getLineContent($data){
        $this->setLineData($data);
        $newLineContent = $this->getLayout()
            ->createBlock('cpcore/adminhtml_replacer_edit_dynamictable_newline')
            ->toHtml();

        return $newLineContent;
    }

    public function getLines(){
        if (!$this->getReplacerId()){
            return false;
        }
        if (!$this->_lines){
            $this->_lines = $this->_getValuesModel()->getLinesByReplacer($this->getReplacerId());
        }

        if (count($this->_lines)==0){
            return false;
        }

        return $this->_lines;
    }

    public function getCountLines(){
        return count($this->getLines());
    }

    protected function _getValuesModel(){
        return Mage::getSingleton('cpreplacer/replacer_sub');
    }

    public function getReplacerId(){
        $replacerId = $this->getReplacer()->getId();
        if ($replacerId){
            return $replacerId;
        }
        return false;
    }


    protected function setLineData($data){
        Mage::unregister('cpcore_replacer_line_data');
        Mage::register('cpcore_replacer_line_data',$data);

        return $this;
    }

    public function getLineData(){
        return Mage::registry('cpcore_replacer_line_data');
    }

    public function getMaxLineIndex(){

        $lines =  $this->getLines();
        if (!$lines){
            return 0;
        }
        $maxIndex = 0;
        foreach($lines as $line){
            if ($line[0]['line_id']>$maxIndex){
                $maxIndex = $line[0]['line_id'];
            }
        }
        return $maxIndex;
    }


}
