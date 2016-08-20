<?php
class Neklo_ABTesting_Model_Abtestpresentation extends Mage_Core_Model_Abstract {
    
    protected function _construct() {
        parent::_construct();
        $this->_init('neklo_abtesting/abtestpresentation'); 
    }

    public function deleteByAbTestId($abTestId) { 
    	$links = $this->getCollection()->addFieldToFilter('abtest_id', $abTestId);

    	foreach ($links as $link) {
    		$link->delete();
    	}
    } 

    public function loadByAbTestId($abTestId) { 
    	$links = $this->getCollection()->addFieldToFilter('abtest_id', $abTestId);
    	return $links;
    }

    public function loadByAbTestPresentationId($abTestId, $abPresentationId) { 
        $links = $this->getCollection()
                 ->addFieldToFilter('abtest_id', $abTestId)
                 ->addFieldToFilter('abpresentation_id', $abPresentationId)
                 ;
        
        return $links->getFirstItem();
    }

    public function deleteByAbTestPresentationId($abTestId, $presentationId) { 
        $links = $this->getCollection()->addFieldToFilter('abtest_id', $abTestId)->addFieldToFilter('abpresentation_id', $presentationId);
        foreach ($links as $link) {
            $link->delete();
        }
    }

    public function updatePresentationsForAbTest($presentations, $abTest) { 
        $abTestId = $abTest->getId();

        $update = array();
        $linksToDelete = array();

        foreach ($presentations as $presentation) {
            if(!isset($presentation['delete']) || $presentation['delete'] != 1) { 
                $update[] = array('abtest_id' => $abTestId, 'abpresentation_id' => $presentation['presentation_id'], 'chance' => $presentation['chance']);
            } 
            else { $linksToDelete[] = $presentation['presentation_id']; }
        }

        foreach ($linksToDelete as $presentationId) {
            $this->deleteByAbTestPresentationId($abTestId, $presentationId);
        }
    
        foreach ($update as $presentationLink) {
            $existedPresentation = $this->loadByAbTestPresentationId($abTestId, $presentationLink['abpresentation_id']);
            if($existedPresentation->getData('id')) { 
                $existedPresentation->addData($presentationLink)->save();
            } else { 
                Mage::getModel('neklo_abtesting/abtestpresentation')->load(0)->addData($presentationLink)->save();
            }
        }
    }
}