<?php
/**
 * Status model
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Model_Status extends Varien_Object
{
	/*
	 * Status values
	 */
    const STATUS_ENABLED	    = 1;
    const STATUS_DISABLED	= 2;

    /**
     * Get statuses as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('dcontent')->__('Enabled'),
            self::STATUS_DISABLED   => Mage::helper('dcontent')->__('Disabled')
        );
    }
}