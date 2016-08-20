<?php
class Neklo_ABTesting_Model_Layout_Update extends Mage_Core_Model_Layout_Update
{
    public function getCacheId() {
        if (!$this->_cacheId) {
            $handles = $this->getHandles();
            
            // fix for A/B-testing
            if(Mage::app()->getStore()->getId() != 0) { 
                $abtestHash = $this->getVariantsHash();
                if ($abtestHash) $handles[] = $abtestHash;
            }
            
            $this->_cacheId = 'LAYOUT_'.Mage::app()->getStore()->getId().md5(join('__', $handles));
        }
        return $this->_cacheId;
    }

    public function getVariantsHash() { 
        $hash = md5(Mage::helper('neklo_abtesting')->getVariantsHash());
        return $hash;
    }    


}
