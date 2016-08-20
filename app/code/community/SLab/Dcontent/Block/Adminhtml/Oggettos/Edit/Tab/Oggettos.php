<?php
/**
 * Block edit page - products grid
 *
 * @category    SLab
 * @package     SLab_Dcontent
 * @author      SLabweb team
 */
class SLab_Dcontent_Block_Adminhtml_Oggettos_Edit_Tab_Oggettos extends Mage_Adminhtml_Block_Widget_Grid
{
   /**
   * Prepare  grid
   *
   * 
   */
    public function __construct()
    {
        parent::__construct();
        $this->setId('block_product_grid');
        $this->setUseAjax(true);
        if ($this->_getBlock()->getId()) {
            $this->setDefaultFilter(array('in_products'=>1));
        }
    }

   /**
   * Get current block 
   *
   * return SLab_Dcontent_Model_Dcontent
   */
    protected function _getBlock()
    {
    	return Mage::getModel('dcontent/oggettos')->load($this->getRequest()->getParam('id'));
    }
    
    /**
   * Apply filter to products list
   *
   * @return this
   */
       protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->_getProduct()->getUpsellReadonly();
    }

     /**
     * Prepare grid collection object
     *
     * @return this
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('score/oggetto_link')
            ->getOggettoCollection()
            ->addAttributeToSelect('*');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid collumns
     *
     * @return this
     */
    protected function _prepareColumns()
    {
            $this->addColumn('in_products', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id'
            ));
    
        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '100px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('score/oggetto_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '130px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '90px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('score/oggetto_status')->getOptionArray(),
        ));

        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '90px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getSingleton('score/oggetto_visibility')->getOptionArray(),
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));
      

        $this->addColumn('position', array(
            'header'    => Mage::helper('catalog')->__('Position'),
            'name'      => 'position[]',
            'type'      => 'number',
            'width'     => '60px',
            'validate_class' => 'validate-number',
            'index'     => 'position',
        	'value'		=> '0',
            'editable'  => true,
            'edit_only' => false
        ));

        return parent::_prepareColumns();
    }
    
    /**
     * Prepare grid url
     *
     * @return sting
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('*/*/products', array('_current'=>true));
    }
    
    /**
     * Get selected products of current block
     *
     * @return array $products
     */
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('oggettos', null);
        
        $id     = $this->getRequest()->getParam('id');
		$block  = Mage::getModel('dcontent/oggettos')->load($id);
		
        if (!is_array($products)) {
        	$products = array();
        	
        	if($block->getOggettos()!='') {
	        	$arr = explode('&',$block->getOggettos());
	        	foreach($arr as $p) {
	        		list($id,$pos) = explode('=',$p);	
	        		$products[] = $id; 
	        	}
        	}
        }
        return $products;
    }
}