<?php 

class Snowcommerce_Seo_Model_Convert_Adapter_Content extends Mage_Eav_Model_Convert_Adapter_Entity
{
    protected $_categoryCache = array();

    protected $_stores;

    public function parse()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            //print '<pre>'.memory_get_usage().'</pre>';
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();
            $this->saveRow($importData);
        }
    }

    /**
     * Save category (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
    		$ids = Mage::getModel('seo/seo')->getCollection()->getAllIds();
    		$content = Mage::getModel('seo/seo');
    		$collection = Mage::getModel('seo/seo')->getCollection()->addFieldToFilter('url',urlencode($importData['url']));
//                echo $collection->getSelect();
//                echo count($collection);
//                echo $collection->getSize(); die;
    		
                if(count($collection)) {
                    foreach($collection as $url) {
                        $import_id = $url->getSeoId();
                    }
                     $new = 0;
                }
                elseif(isset($importData['seo_id']) && trim($importData['seo_id'])!='' && $content->load($importData['seo_id'])) {
                    $import_id = $importData['seo_id'];
                    $new = 0;
                }
    		else { $import_id = array_pop($ids)+1; $new = 1;}
    		
    		$importData['url'] = urlencode($importData['url']);
    		
    		if(!$new) {
                    
    			$content->load($import_id);
    			$content->addData($importData);
    			$content->save();
	    	}
	    	else {
	    	 	$write = $this->getConnection();
			    $table = $this->getTable('seo');
		        try {
		            if (!$write->fetchOne("select * from $table where seo_id=".$import_id)) {
		               $write->query("insert into $table (seo_id) values (".$import_id.")");
		    	    }
		    	 $content->load($import_id);
		    	 $importData['seo_id'] = $import_id;
		    	 $content->setData($importData);
    			 $content->save();
		    	} catch (Exception $e) {
		            throw $e;
		        }
	    	}
    		
    		
    		
			return true;
	}
	
	public function getConnection()
	{
		return Mage::getSingleton('core/resource')->getConnection('catalog_write');
	}


	public function getTable($table)
	{
		return Mage::getSingleton('core/resource')->getTableName($table);
	}
	
	
	protected function userCSVDataAsArray($data) {
		return explode(';', str_replace(" ", "", $data));
	}
	
	/**
     *  Init stores
     *
     *  @param    none
     *  @return      void
     */
    protected function _initStores ()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true, true);
            foreach ($this->_stores as $code => $store) {
                $this->_storesIdCode[$store->getId()] = $code;
            }
        }
    }
}

	


?> 
