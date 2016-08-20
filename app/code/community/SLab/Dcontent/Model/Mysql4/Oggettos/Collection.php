<?php
/**
 * Product blocks collection
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Model_Mysql4_Oggettos_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
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
}