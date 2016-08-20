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
 * Configurable oggetto data retreiver
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_ScoreIndex_Model_Data_Configurable extends Shaurmalab_ScoreIndex_Model_Data_Abstract
{
    /**
     * Defines when oggetto type has children
     *
     * @var boolean
     */
    protected $_haveChildren = array(
                        Shaurmalab_ScoreIndex_Model_Retreiver::CHILDREN_FOR_TIERS=>false,
                        Shaurmalab_ScoreIndex_Model_Retreiver::CHILDREN_FOR_PRICES=>false,
                        Shaurmalab_ScoreIndex_Model_Retreiver::CHILDREN_FOR_ATTRIBUTES=>true,
                        );

    /**
     * Defines when oggetto type has parents
     *
     * @var boolean
     */
    protected $_haveParents = false;

    protected function _construct()
    {
        $this->_init('scoreindex/data_configurable');
    }

   /**
     * Retreive oggetto type code
     *
     * @return string
     */
    public function getTypeCode()
    {
        return Shaurmalab_Score_Model_Oggetto_Type::TYPE_CONFIGURABLE;
    }

    /**
     * Get child link table and field settings
     *
     * @return mixed
     */
    protected function _getLinkSettings()
    {
        return array(
                    'table'=>'score/oggetto_super_link',
                    'parent_field'=>'parent_id',
                    'child_field'=>'oggetto_id',
                    );
    }
}
