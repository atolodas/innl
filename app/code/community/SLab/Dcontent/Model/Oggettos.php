<?php
/**
 * Product block model
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Model_Oggettos extends Mage_Core_Model_Abstract
{
    /**
     * Init resource model
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('dcontent/oggettos');
    }
    
    /**
     * Load block by id
     *
     * @param integer $id
     * @return this
     */
    public function getBlockById($id) {
    	$block = Mage::getModel('dcontent/oggettos')->load($id);
    	$this->setOptions($block);
	    if($block->getStatus()!='2') {	
	    	$products_str = $block->getOggettos();
	    	$products_arr = Mage::helper('dcontent')->decodeInput($products_str);
			$pcollection = array();
			foreach($products_arr as $id=>$p) { 
				$pcollection[$id] = $p['position'];
			}
			$this->setOggettos($pcollection);    	
    	}
		return $this;
    }
    
    /**
     * Get blocks by product id
     *
     * @param integer $id
     * @param string $type
     * @return SLab_Dcontent_Model_Mysql4_Dcontent_Collection
     */
    public function getBlocksByProduct($id,$type) {
    	$blocks = Mage::getModel('dcontent/oggettos')->getResourceCollection()->addFieldToFilter('products',array(array('like'=>"$id=%"),array('like'=>"%&$id=%")))->addFieldToFilter('block_type',$type);
	    return $blocks;
    }

    public function toOptionArray(){
        $collection = $this->getCollection()->addFilter('status',1)->addOrder('title','asc');
        $option_array = array();
        foreach($collection as $webform)
            $option_array[]= array('value'=>$webform->getId(), 'label' => $webform->getTitle());
        return $option_array;
    }
}