<?php
/**
 * Blocks resource model
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Model_Mysql4_Dcontent extends Mage_Core_Model_Mysql4_Abstract
{
	/**
     * Init table and FK
     *
     */
    public function _construct()
    {    
        // Note that the dcontent_id refers to the key field in your database table.
        $this->_init('dcontent/dcontent', 'dcontent_id');
    }
}