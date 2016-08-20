<?php
class Neklo_ABTesting_Block_Adminhtml_System_Abtest_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('abtestGrid');
        $this->setDefaultSort('abtest_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('abtest_filter');

    }

    protected function _prepareCollection() {        
        $collection = Mage::getModel('neklo_abtesting/abtest')->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns() {
        $helper = Mage::helper('neklo_abtesting');
        
        $this->addColumn('abtest_id',
            array(
                'header'=> $helper->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'abtest_id',
        ));
        
        
        $this->addColumn('name', array(
            'header' => $helper->__('A/B Test Title'),
            'index' => 'name',
            'type' => 'text',
        ));
        
        $this->addColumn('cookie_lifetime',
            array(
                'header'=> $helper->__('Cookie Lifetime'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'cookie_lifetime',
        ));

        $this->addColumn('start_at', array(
            'header' => $helper->__('Start at'),
            'index' => 'start_at',
            'type' => 'date',
            'width' => '100px',
        ));

        $this->addColumn('end_at', array(
            'header' => $helper->__('End at'),
            'index' => 'end_at',
            'type' => 'date',
            'width' => '100px',
        ));

        $this->addColumn('created_at', array(
            'header' => $helper->__('Created'),
            'index' => 'created_at',
            'type' => 'date',
            'width' => '100px',
        ));

        $this->addColumn('status', array(
            'header' => $helper->__('Status'),
            'index' => 'status',
            'type'  => 'options',
            'options' => array(
                '1' => Mage::helper('checkout')->__('Enabled'),
                '0' => Mage::helper('checkout')->__('Disabled'),
            ),
            'width' => '100px',
        ));
        
        $this->addColumn('action',
            array(
                'header'    => Mage::helper('catalog')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'     => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('catalog')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit'
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        return $this;
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id'=>$row->getId())
        );
    }
}
