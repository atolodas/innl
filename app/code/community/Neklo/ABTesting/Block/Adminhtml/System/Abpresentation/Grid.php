<?php
class Neklo_ABTesting_Block_Adminhtml_System_Abpresentation_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('abpresentationGrid');
        $this->setDefaultSort('presentation_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('abtest_filter');

    }

    protected function _prepareCollection() {        
        $collection = Mage::getModel('neklo_abtesting/abpresentation')->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns() {
        $helper = Mage::helper('neklo_abtesting');
        
        $this->addColumn('presentation_id',
            array(
                'header'=> $helper->__('ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'presentation_id',
        ));
        
        
        $this->addColumn('name', array(
            'header' => $helper->__('A/B Presentation Title'),
            'index' => 'name',
            'type' => 'text',
        ));

        $this->addColumn('code', array(
            'header' => $helper->__('A/B Presentation Code'),
            'index' => 'code',
            'type' => 'text',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('catalog')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => array(
                '1' => Mage::helper('checkout')->__('Enabled'),
                '0' => Mage::helper('checkout')->__('Disabled'),
            ),
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
