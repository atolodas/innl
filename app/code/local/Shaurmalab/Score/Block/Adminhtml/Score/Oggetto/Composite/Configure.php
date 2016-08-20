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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml score entity composite configure block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Composite_Configure extends Mage_Adminhtml_Block_Widget
{
    protected $_entity;

    /**
     * Set template
     */
    protected function _construct()
    {
        $this->setTemplate('score/oggetto/composite/configure.phtml');
    }

    /**
     * Retrieve entity object
     *
     * @return Shaurmalab_Score_Model_Oggetto
     */
    public function getOggetto()
    {
        if (!$this->_entity) {
            if (Mage::registry('current_entity')) {
                $this->_entity = Mage::registry('current_entity');
            } else {
                $this->_entity = Mage::getSingleton('score/oggetto');
            }
        }
        return $this->_entity;
    }

    /**
     * Set entity object
     *
     * @param Shaurmalab_Score_Model_Oggetto $entity
     * @return Shaurmalab_Score_Block_Adminhtml_Score_Oggetto_Composite_Configure
     */
    public function setOggetto(Shaurmalab_Score_Model_Oggetto $entity = null)
    {
        $this->_entity = $entity;
        return $this;
    }
}