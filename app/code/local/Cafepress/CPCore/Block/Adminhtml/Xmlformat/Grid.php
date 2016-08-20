<?php

class Cafepress_CPCore_Block_Adminhtml_Xmlformat_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('wmsGrid');
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

    /**
     * Init ordered product collection
     * @return void
     */
    protected function _prepareCollection()
    {
        $store = $this->_getStore();
        $collection = Mage::getModel('cpcore/xmlformat')->getCollection()
                ->addAttributeToSelect('*');

        $this->setCollection($collection);
        $collection->addFilterIfNotDeveloper();

        if($store->getId()) {
            $collection->addStoreFilter($store->getId());
        }
        $this->setCollection($collection);

        parent::_prepareCollection();
//        return $this;
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
            'width' => '50px',
            'type'  => 'number',
            'index' => 'entity_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('cpcore')->__('Format name'),
            'align' => 'right',
            'index' => 'name',
            'type'  => 'text'
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('cpcore')->__('Type'),
            'width' => '250px',
            'align' => 'right',
            'index' => 'type' ,
            'type'  => 'options',
            'options' => Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_type')->getOptions(),
        ));

        $this->addColumn('condition', array(
            'header' => Mage::helper('cpcore')->__('Condition'),
            'align' => 'right',
            'index' => 'condition',
            'type'  => 'text'
        ));

        $this->addColumn('precondition', array(
            'header' => Mage::helper('cpcore')->__('Precondition'),
            'align' => 'right',
            'index' => 'precondition',
            'type'  => 'text'
        ));
//
//          $scheduleSource = Mage::getModel('cpcore/resource_eav_mysql4_xmlformat_schedule')->getAllOptions();
//
//          $this->addColumn('schedule', array(
//            'header' => Mage::helper('cpcore')->__('Schedule'),
//            'align' => 'right',
//            'index' => 'schedule',
//            'type'  => 'options',
//
//            'options' => $scheduleSource,
//        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('cpcore')->__('Status'),
            'width' => '200px',
            'align' => 'right',
            'index' => 'status' ,
            'type'  => 'options',
            'options' => Mage::getModel('catalog/product_status')->getOptionArray(),
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('format');

        $statuses = Mage::getModel('catalog/product_status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('cpcore')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

//    public function getRowUrl($row)
//    {
//        return $this->getUrl('*/*/edit', array('id'=>$row->getId()));
//    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }

}
