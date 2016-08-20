<?php

class Cafepress_CPWms_Model_Replacer_Line extends Mage_Core_Model_Abstract
{
    
    protected $_replacerId = false;
    
    protected function _construct() {
        $this->_init('wmsreplacer/replacer_line');
    }
    
    public function setReplacerId($replacer){
        $this->_replacerId = $replacer;
        return $this;
    }

    public function getReplacerId(){
        if (!$this->_replacerId){

        }
        return $this->_replacerId;
    }
    
   public function setValues($values){
        foreach($values as $lineId => $lineData){
            if ($lineData['delete']=='true'){
                $this->deleteLineByReplacerIdByLineId($this->getReplacerId(), $lineId);
                continue;
            }

            $data = array(
                'default_value' => $lineData['default'],
                'type' => $lineData['type'],
                'replacer_id' => $this->getReplacerId(),
                'line_id' => $lineId,
            );
            $this->addValue($data);
        }
    }
    
   public function deleteLineByReplacerIdByLineId($replacerId, $lineId){
        $collection = $this->getCollection()
            ->addFilter('replacer_id',$replacerId)
            ->addFilter('line_id',$lineId);
        foreach ($collection as $sub){
            $sub->delete();
        }
        
    }
    
   public function deleteLineByReplacerId($replacerId){
        $collection = $this->getCollection()
            ->addFilter('replacer_id',$replacerId);
        foreach ($collection as $sub){
            $sub->delete();
        }
        
    }
    
    public function addValue($data){
        $item = $this->getLineByReplacerIdByLineId($data);
        if(!$item){
            $this->setData($data)
                ->save();
        } else {
            $item->addData($data)
                ->save();
        }
        return $this;

    }

    public function getLineByReplacerIdByLineId($data){
        $item = $this->getCollection()
//            ->addFilter('replacer_id',$data['replacer_id'])
            ->addFilter('line_id',$data['line_id'])
            ->getFirstItem()
        ;
        if (!$item->getId()){
            return false;
        }

        return $item;
    }
    
    public function loadByLineId($lineId){
        $item = $this->getCollection()
            ->addFilter('line_id',$lineId)
            ->getFirstItem()
        ;
        if (!$item->getId()){
            return false;
        }

        return $item;
    }
}
