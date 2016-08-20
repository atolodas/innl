<?php
/**
 * Serializer for products grid
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Dcontent_Edit_Tab_Ajax_Serializer extends Mage_Core_Block_Template
{
	/**
     * Init serializer
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('dcontent/edit/serializer.phtml');
        return $this;
    }

    /**
     * Get products of current Product block in JSON format
     *
     */
    public function getProductsJSON()
    {
        $result = array();
        if ($this->getProducts()) {
            $isEntityId = $this->getIsEntityId();
            foreach ($this->getProducts() as $product) {
                $id = $isEntityId ? $product->getEntityId() : $product->getId();
                $result[$id] = $product->toArray(array('qty', 'position'));
                
            }
        }
        return $result ? Zend_Json_Encoder::encode($result) : '{}'; 
    }
}