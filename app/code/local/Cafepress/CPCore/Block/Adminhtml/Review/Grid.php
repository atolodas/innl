<?php

class Cafepress_CPCore_Block_Adminhtml_Review_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('wmsGridReview');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('xmlformat_filter');
    }
    
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    protected function _getStoreIdByOrder()
    {
        $orderIncrementId = (int)$this->getRequest()->getParam('order', false);
        if ($orderIncrementId){
            $order = Mage::getModel('cpcore/sales_order')->loadByIncrementId($orderIncrementId);
            $this->getRequest()->setParam('store', $order->getStoreId());
            return $order->getStoreId();
        }
        return false;
    }
    
    /**
     * Init ordered product collection
     * @return void
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $storeIdByOrder = $this->_getStoreIdByOrder();
        
        $collection = Mage::getModel('cpcore/xmlformat')->getCollection()
                ->addAttributeToSelect('*');
        $collection->addFilterIfNotDeveloper();
        
        if ($storeIdByOrder){
            $collection->addStoreFilter($storeIdByOrder);
        } else {
            if(!$store->getId() && !Mage::app()->isSingleStoreMode()) {
//            $collection->_setDefaultItems(); //is delete duplicate row
                $allStores = Mage::app()->getStores();

                foreach ($allStores as $store) {
                    $collection2 = Mage::getModel('cpcore/xmlformat')->getCollection()
                        ->addAttributeToSelect('*')
                        ->addStoreFilter($store->getId());

                    $collection->mergeCollections(array($collection2), $store->getId());
                }
            }
        }
        
        #TODO INL: correctly count the number of records in grid
//        Zend_Debug::dump($collection->getSize());
//        Zend_Debug::dump(count($collection->getItems()));
//        Zend_Debug::dump($collection->getItems());
        
        $this->setCollection($collection);
        
        parent::_prepareCollection();
        return $this;
    }
    
    protected function _addColumnFilterToCollection($column)
    {
        return parent::_addColumnFilterToCollection($column);
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'=> Mage::helper('cpcore')->__('ID'),
            'width' => '30px',
            'type'  => 'number',
            'index' => 'entity_id',
        ));
        
        $this->addColumn('name', array(
            'header' => Mage::helper('cpcore')->__('Name'),
            'width' => '150px',
            'align' => 'right',
            'index' => 'name',
            'type'  => 'text',
        ));
        
        $this->addColumn('url', array(
            'header' => Mage::helper('cpcore')->__('Url'),
            'width' => '300px',
//            'align' => 'right',
            'index' => 'entity_id',
            'type'  => 'text',
            'renderer' => new Cafepress_CPCore_Block_Adminhtml_Review_Grid_Renderer_Url()
        ));
        
        $this->addColumn('request', array(
            'header' => Mage::helper('cpcore')->__('Request'),
//            'width' => '500px',
//            'align' => 'right',
            'index' => 'entity_id',
            'type'  => 'textarea',
            'renderer' => new Cafepress_CPCore_Block_Adminhtml_Review_Grid_Renderer_Request()
        ));
        
        $this->addColumn('schedule', array(
            'header' => Mage::helper('cpcore')->__('Schedule'),
            'width' => '50px',
            'align' => 'right',
            'index' => 'schedule',
            'type'  => 'options',
            'options' => Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_schedule')->getOptionArray(),
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('cpcore')->__('Status'),
            'width' => '50px',
            'align' => 'right',
            'index' => 'status' ,
            'type'  => 'options',
            'options' => Mage::getModel('catalog/product_status')->getOptionArray(),
        ));
        
        $this->addColumn('response', array(
            'header' => Mage::helper('cpcore')->__('Response'),
//            'width' => '200px',
            'index' => 'response',
            'type'  => 'text',
            'renderer' => new Cafepress_CPCore_Block_Adminhtml_Review_Grid_Renderer_Response()
        ));
        
        if (!Mage::app()->isSingleStoreMode()){
            if ($this->_getStoreIdByOrder()){
                $this->addColumn('store', array(
                    'header' => Mage::helper('cpcore')->__('Store'),
                    'width' => '80px',
//                    'align' => 'right',
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => $this->_getStoreIdByOrder(),
                    'renderer' => new Cafepress_CPCore_Block_Adminhtml_Review_Grid_Renderer_Store()
                ));
            } else {
                $this->addColumn('store', array(
                    'header' => Mage::helper('cpcore')->__('Store'),
                    'width' => '80px',
                    'align' => 'right',
                    'filter'    => false,
                    'sortable'  => false,
                    'index'     => 'store_id',
                    'type'      => 'options',
                    'options'   => Mage::getModel('core/website')->getCollection()->toOptionHash(),
                ));
            }
            
        }
        
        
        return parent::_prepareColumns();
    }
    
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return false;
//        return $this->getUrl('*/*/#', array('id'=>$row->getId()));
    }
    
//    public function getRowUrl($row)
//    {
//        return $this->getUrl('*/*/edit', array(
//            'store'=>$this->getRequest()->getParam('store'),
//            'id'=>$row->getId())
//        );
//    }

}
