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
 * @package     Shaurmalab_Score
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Score super oggetto link collection
 *
 * @category    Mage
 * @package     Shaurmalab_Score
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Oggetto_Collection
    extends Shaurmalab_Score_Model_Resource_Oggetto_Collection
{
    /**
     * Link table name
     *
     * @var string
     */
    protected $_linkTable;

    /**
     * Assign link table name
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_linkTable = $this->getTable('score/oggetto_super_link');
    }

    /**
     * Init select
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Oggetto_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->join(array('link_table' => $this->_linkTable),
            'link_table.oggetto_id = e.entity_id',
            array('parent_id')
        );

        return $this;
    }

    /**
     * Set Oggetto filter to result
     *
     * @param Shaurmalab_Score_Model_Oggetto $oggetto
     * @return Shaurmalab_Score_Model_Resource_Oggetto_Type_Configurable_Oggetto_Collection
     */
    public function setOggettoFilter($oggetto)
    {
        $this->getSelect()->where('link_table.parent_id = ?', (int) $oggetto->getId());
        return $this;
    }

    /**
     * Retrieve is flat enabled flag
     * Return alvays false if magento run admin
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        return false;
    }
}
