<?php

class Cafepress_CPCore_Model_Replacer_Sub extends Mage_Core_Model_Abstract
{
    protected $_replacer = false;

    protected function _construct() {
        $this->_init('cpreplacer/replacer_sub');
    }
    
    public function getLineModel(){
        return Mage::getSingleton('cpcore/replacer_line');
    }

    public function setReplacer($replacer){
        $this->_replacer = $replacer;
        return $this;
    }

    public function getReplacer(){
        if (!$this->_replacer){

        }
        return $this->_replacer;
    }


    public function getDefaultValues($replacerId)
    {
        $subCollection = $this->getCollection()
            ->addFilter('replacer_id',$replacerId)
            ->addFilter('store_id',0)
        ;
        $result  = array();
        foreach($subCollection as $sub){
            $result[] = $sub->getValue();
        }

        return $result;
    }

    public function setValues($values){
        foreach($values as $lineId => $lineData){
            if ($lineData['delete']=='true'){
                $this->deleteLineByReplacerIdByLineId($this->getReplacerId(), $lineId);
                continue;
            }

            $data = array(
                'value' => $lineData['default'],
                'replacer_id' => $this->getReplacerId(),
                'line_id' => $lineId,
                'store_id' => '0',
            );
            $this->addValue($data);

            foreach($lineData['store_value'] as $key=>$val){
                $data = array(
                    'value' => $val,
                    'replacer_id' => $this->getReplacerId(),
                    'line_id' => $lineId,
                    'store_id' => $key,
                );
                $this->addValue($data);

            }
            $this->getLineModel()->setReplacerId($this->getReplacerId())->setValues($values);
        }
    }

    public function addValue($data){
        $item = $this->getValueByReplacerIdByLineIdByStoreId($data);
        if(!$item){
            $this->setData($data)
                ->save();
        } else {
            $this->load($item->getId())
                ->addData($data)
                ->save();
        }
        return $this;

    }

    public function getValueByReplacerIdByLineIdByStoreId($data){
        $item = $this->getCollection()
            ->addFilter('replacer_id',$data['replacer_id'])
            ->addFilter('line_id',$data['line_id'])
            ->addFilter('store_id',$data['store_id'])
            ->getFirstItem()
        ;
        if (!$item->getId()){
            return false;
        }

        return $item;
    }

    public function getLinesByReplacer($replacerId)
    {
        $collection = $this->getCollection()
            ->addFilter('replacer_id',$replacerId);
        $lineIds = $this->_getLineIdsByReplacer($replacerId,$collection);

        $result = array();
        foreach($lineIds as $lineId){
            $line = $this->getLineModel()->loadByLineId($lineId);
            foreach($collection as $item){
                if ($item->getLineId() == $lineId){
                    $result[$lineId][$item->getStoreId()] = $item->getData();
                    $result[$lineId]['type'] = $line->getType();
                }
            }
        }
        return $result;
    }

    protected function _getLineIdsByReplacer($replacerId, $collection = false){
        if (!$collection){
            $collection = $this->getCollection()
                ->addFilter('replacer_id',$replacerId);
        }

        $lineId = array();
        foreach($collection as $item){
            $lineId[] = $item->getLineId();
        }
        return array_unique($lineId);
    }

    public function deleteLineByReplacerIdByLineId($replacerId, $lineId){
        $collection = $this->getCollection()
            ->addFilter('replacer_id',$replacerId)
            ->addFilter('line_id',$lineId);
        foreach ($collection as $sub){
            $sub->delete();
        }
        $this->getLineModel()->deleteLineByReplacerIdByLineId($replacerId, $lineId);
        
    }

    public function deleteLineByReplacer($replacerId){
        $collection = $this->getCollection()
            ->addFilter('replacer_id',$replacerId);
        foreach ($collection as $sub){
            $sub->delete();
        }
        $this->getLineModel()->deleteLineByReplacerId($replacerId);
    }

    public function getStores(){
        return Mage::helper('cpcore/replacer')->getStores();
    }
}
