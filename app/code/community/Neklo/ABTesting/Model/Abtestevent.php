<?php
class Neklo_ABTesting_Model_Abtestevent extends Mage_Core_Model_Abstract {
    
    protected function _construct() {
        parent::_construct();
        $this->_init('neklo_abtesting/abtestevent');
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

    public function loadByAbTestEventId($abTestId, $eventId) { 
        $links = $this->getCollection()->addFieldToFilter('abtest_id', $abTestId)->addFieldToFilter('event_id', $eventId);
        return $links->getFirstItem();
    }

    public function deleteByAbTestEventId($abTestId, $eventId) { 
        $link = $this->getCollection()->addFieldToFilter('abtest_id', $abTestId)->addFieldToFilter('event_id', $eventId)->getFirstItem();
        $link->delete();
    }
   
    public function updateEventsForAbTest($events, $abTest) { 

        $abTestId = $abTest->getId();

        foreach ($events as $eventId) {
            $existedEvent = $this->loadByAbTestEventId($abTestId, $eventId);
            if(!$existedEvent->getId()) { 
                $this->load(0)->setData(array('abtest_id' => $abTestId, 'event_id' => $eventId))->save();
            }
        }

        if($linksToDelete = array_diff($abTest->getSuccessEvents()->getColumnValues('event_id'), $events)) { 
            foreach ($linksToDelete as $eventId) {
                $this->deleteByAbTestEventId($abTestId, $eventId);
            }
        }
    }
}