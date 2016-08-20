<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Shaurmalab_ScoreIndex
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Grouped oggetto data retreiver
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreIndex_Model_Data_Grouped extends Shaurmalab_ScoreIndex_Model_Data_Abstract
{
    /**
     * Defines when oggetto type has parents
     *
     * @var boolean
     */
    protected $_haveParents = false;

    protected function _construct()
    {
        $this->_init('scoreindex/data_grouped');
    }

    /**
     * Fetch final price for oggetto
     *
     * @param int $oggetto
     * @param Mage_Core_Model_Store $store
     * @param Mage_Customer_Model_Group $group
     * @return float
     */
    public function getFinalPrice($oggetto, $store, $group)
    {
        return false;
    }

    /**
     * Retreive oggetto type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return Shaurmalab_Score_Model_Oggetto_Type::TYPE_GROUPED;
    }

    /**
     * Get child link table and field settings
     *
     * @return mixed
     */
    protected function _getLinkSettings()
    {
        return array(
                    'table'=>'score/oggetto_link',
                    'parent_field'=>'oggetto_id',
                    'child_field'=>'linked_oggetto_id',
                    'additional'=>array('link_type_id'=>Shaurmalab_Score_Model_Oggetto_Link::LINK_TYPE_GROUPED)
                    );
    }
}
