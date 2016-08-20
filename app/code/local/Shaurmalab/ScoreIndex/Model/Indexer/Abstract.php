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
 * Score indexer abstract class
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Shaurmalab_ScoreIndex_Model_Indexer_Abstract
    extends Mage_Core_Model_Abstract
    implements Shaurmalab_ScoreIndex_Model_Indexer_Interface
{
    protected $_processChildren = true;
    protected $_processChildrenForConfigurable = true;
    protected $_runOnce = false;

    public function processAfterSave(Shaurmalab_Score_Model_Oggetto $object, $forceId = null)
    {
        $associated = array();
        switch ($object->getTypeId()) {
            case Shaurmalab_Score_Model_Oggetto_Type::TYPE_GROUPED:
                $associated = $object->getTypeInstance(true)->getAssociatedOggettos($object);
                break;

            case Shaurmalab_Score_Model_Oggetto_Type::TYPE_CONFIGURABLE:
                $associated = $object->getTypeInstance(true)->getUsedOggettos(null, $object);
                break;
        }

        if (!$this->_isObjectIndexable($object) && is_null($forceId)) {
            return;
        }

        $data = array();

        if ($this->_runOnce) {
            $data = $this->createIndexData($object);
        } else {
            $attributes = $object->getAttributes();
            foreach ($attributes as $attribute) {
                if ($this->_isAttributeIndexable($attribute) && $object->getData($attribute->getAttributeCode()) != null) {
                    $row = $this->createIndexData($object, $attribute);
                    if ($row && is_array($row)) {
                        if (isset($row[0]) && is_array($row[0])) {
                            $data = array_merge($data, $row);
                        } else {
                            $data[] = $row;
                        }
                    }
                }
            }
        }
        $function = 'saveIndex';
        if ($data && is_array($data)) {
            if (isset($data[0]) && is_array($data[0]))
                $function = 'saveIndices';

            $this->$function($data, $object->getStoreId(), ($forceId != null ? $forceId : $object->getId()));
        }

        if (!$this->_processChildrenForConfigurable && $object->getTypeId() == Shaurmalab_Score_Model_Oggetto_Type::TYPE_CONFIGURABLE)
            return;

        if ($associated && $this->_processChildren) {

            foreach ($associated as $child) {
                $child
                    ->setStoreId($object->getStoreId())
                    ->setWebsiteId($object->getWebsiteId());
                $this->processAfterSave($child, $object->getId());
            }
        }
    }

    public function saveIndex($data, $storeId, $oggettoId)
    {
        $this->_getResource()->saveIndex($data, $storeId, $oggettoId);
    }

    public function saveIndices(array $data, $storeId, $oggettoId)
    {
        $this->_getResource()->saveIndices($data, $storeId, $oggettoId);
    }

    protected function _isObjectIndexable(Shaurmalab_Score_Model_Oggetto $object)
    {
        if ($object->getStatus() != Shaurmalab_Score_Model_Oggetto_Status::STATUS_ENABLED) {
            return false;
        }

        if ($object->getVisibility() != Shaurmalab_Score_Model_Oggetto_Visibility::VISIBILITY_IN_CATALOG &&
            $object->getVisibility() != Shaurmalab_Score_Model_Oggetto_Visibility::VISIBILITY_BOTH) {
            return false;
        }

        return true;
    }

    public function isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        return $this->_isAttributeIndexable($attribute);
    }

    protected function _isAttributeIndexable(Mage_Eav_Model_Entity_Attribute_Abstract $attribute)
    {
        return true;
    }

    public function getIndexableAttributeCodes()
    {
        return $this->_getResource()->loadAttributeCodesByCondition($this->_getIndexableAttributeConditions());
    }

    protected function _getIndexableAttributeConditions()
    {
        return array();
    }

    public function cleanup($oggettoId, $storeId = null)
    {
        $this->_getResource()->cleanup($oggettoId, $storeId);
    }

    public function isAttributeIdUsed()
    {
        return true;
    }
}
