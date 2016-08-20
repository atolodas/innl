<?php
class Neklo_ABTesting_Block_Adminhtml_Visitor_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('abtestGrid');
        $this->setDefaultSort('abtest_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        $this->setVarNameFilter('abtest_filter');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('neklo_abtesting/visitor')->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    protected function _prepareColumns()
    {
        $helper = Mage::helper('neklo_abtesting');

        $this->addColumn('visitor_id',
            array(
                'header'=> $helper->__('Visitor ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'visitor_id',
        ));

        $this->addColumn('customer_id',
            array(
                'header'=> $helper->__('Customer ID'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'customer_id',
        ));


        $this->addColumn('is_banned',
            array(
                'header'=> $helper->__('Is Banned?'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'is_banned',
        ));

        $this->addColumn('visitor_info',
            array(
                'header'=> $helper->__('Additional Info'),
                'type'  => 'text',
                'index' => 'visitor_info',
        ));

        $this->addColumn('visits_count',
            array(
                'header'=> $helper->__('Visits Number'),
                'width' => '50px',
                'type'  => 'number',
                'index' => 'visits_count',
        ));

        $this->addColumn('created_at', array(
            'header' => $helper->__('Created at'),
            'index' => 'created_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        $this->addColumn('updated_at', array(
            'header' => $helper->__('Updated at'),
            'index' => 'updated_at',
            'type' => 'datetime',
            'width' => '100px',
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('visitor_id');
        $this->getMassactionBlock()->setFormFieldName('visitor');

        $this->getMassactionBlock()
        ->addItem('ban', array(
             'label'=> Mage::helper('neklo_abtesting')->__('Ban'),
             'url'  => $this->getUrl('*/neklo_abtesting_visitor/massBan'),
             'confirm' => Mage::helper('neklo_abtesting')->__('Are you sure with Ban?')
        ))
        ->addItem('unban', array(
             'label'=> Mage::helper('neklo_abtesting')->__('UnBan'),
             'url'  => $this->getUrl('*/neklo_abtesting_visitor/massUnBan'),
             'confirm' => Mage::helper('neklo_abtesting')->__('Are you sure with UnBan?')
        ));

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return false;
    }
}
