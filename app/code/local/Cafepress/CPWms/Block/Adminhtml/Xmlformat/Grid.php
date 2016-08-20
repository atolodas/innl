<?php

class Cafepress_CPWms_Block_Adminhtml_Xmlformat_Grid extends Mage_Adminhtml_Block_Widget_Grid
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
        $collection = Mage::getModel('cpwms/xmlformat')->getCollection()
                ->addAttributeToSelect('*');

        $this->setCollection($collection);
        $collection->addFilterIfNotDeveloper();

        if($store->getId()) {
            $collection->addStoreFilter($store->getId());
        }
        $this->setCollection($collection);
        
        parent::_prepareCollection();
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
            'header'=> Mage::helper('cpwms')->__('ID'),
            'width' => '50px',
            'type'  => 'number',
            'index' => 'entity_id',
        ));
        
        $this->addColumn('name', array(
            'header' => Mage::helper('cpwms')->__('Format name'),
            'align' => 'right',
            'index' => 'name',
            'type'  => 'text'
        ));

        $this->addColumn('type', array(
            'header' => Mage::helper('cpwms')->__('Type'),
            'width' => '250px',
            'align' => 'right',
            'index' => 'type' ,
            'type'  => 'options',
            'options' => Mage::getModel('cpwms/resource_eav_mysql4_xmlformat_type')->getOptions(),
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('cpwms')->__('Status'),
            'width' => '200px',
            'align' => 'right',
            'index' => 'status' ,
            'type'  => 'options',
            'options' => array(

                array('value'=>'', 'label'=>'Please select'),
                array('value'=>0, 'label'=>'Disabled'),
                array('value'=>1, 'label'=>'Enabled'),
            ),
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('format');

        $statuses = array(
            array('value'=>'', 'label'=>'Please select'),
            array('value'=>0, 'label'=>'Disabled'),
            array('value'=>1, 'label'=>'Enabled'),
        );

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('catalog')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('cpwms')->__('Status'),
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


    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array(
            'store'=>$this->getRequest()->getParam('store'),
            'id'=>$row->getId())
        );
    }

}
