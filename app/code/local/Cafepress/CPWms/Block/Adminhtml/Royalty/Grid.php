<?php

class Cafepress_CPWms_Block_Adminhtml_Royalty_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    /**
     * Sub report size
     *
     * @var int
     */
    protected $_subReportSize = 0;

    /**
     * Initialize Grid settings
     *
     */
    public function __construct()
    {

        parent::__construct();
        $this->setId('gridProductsSold');
    }

    /**
     * Prepare collection object for grid
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('cpwms/report_royalty_collection');


        return $this;
    }

    /**
     * Prepare Grid columns
     *
     * @return Mage_Adminhtml_Block_Report_Product_Sold_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('sku', array(
            'header'    =>Mage::helper('reports')->__('SKU Number'),
            'index'     =>'sku'
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('reports')->__('SKU Description'),
            'index'     =>'name'
        ));

        $this->addColumn('property', array(
            'header'    =>Mage::helper('reports')->__('Property Description'),
            'index'     =>''
        ));

        $this->addColumn('licensed', array(
            'header'    =>Mage::helper('reports')->__('Licensed Product/s'),
            'index'     =>''
        ));

        $this->addColumn('lang', array(
            'header'    =>Mage::helper('reports')->__('Language'),
            'index'     =>''
        ));

        $this->addColumn('retailer', array(
            'header'    =>Mage::helper('reports')->__('Distribution Channel/Retailer'),
            'index'     =>''
        ));


        $this->addColumn('territory', array(
            'header'    =>Mage::helper('reports')->__('Territory/ Sales Country'),
            'index'     =>''
        ));

        $this->addColumn('qty_ordered', array(
            'header'    =>Mage::helper('reports')->__('Sales Units'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'qty_ordered',
            'total'     =>'sum',
            'type'      =>'number'
        ));


        $this->addColumn('product_price', array(
            'header'    =>Mage::helper('reports')->__('Unit price'),
            'index'     =>'product_price',
            'total'     =>'sum',
            'type'      =>'number'
        ));

        $this->addColumn('row_total', array(
            'header'    =>Mage::helper('reports')->__('Gross sales'),
            'index'     =>'row_total',
            'total'     =>'sum',
            'type'      =>'number'
        ));


        $this->addColumn('qty_refunded', array(
            'header'    =>Mage::helper('reports')->__('Adjustment Units'),
            'index'     =>'qty_refunded',
            'total'     =>'sum',
            'type'      =>'number'
        ));
        
        
         $this->addColumn('amount_refunded', array(
            'header'    =>Mage::helper('reports')->__('Adjustment Amounts'),
            'index'     =>'amount_refunded',
            'total'     =>'sum',
            'type'      =>'number'
        ));
        
         $this->addColumn('net_sales', array(
            'header'    =>Mage::helper('reports')->__('Net Sales'),
            'index'     =>'net_sales',
            'total'     =>'sum',
            'type'      =>'number'
        ));
        
         

 $this->addColumn('Marketing_Fund', array(
            'header'    =>Mage::helper('reports')->__('Marketing Fund'),
            'index'     =>''
        ));


 $this->addColumn('Marketing_Fund_Percent', array(
            'header'    =>Mage::helper('reports')->__('Marketing Fund %'),
            'index'     =>''
        ));

$this->addColumn('Royalty', array(
            'header'    =>Mage::helper('reports')->__('Royalty'),
            'index'     =>''
        ));


 $this->addColumn('Royalty_Percent', array(
            'header'    =>Mage::helper('reports')->__('Royalty %'),
            'index'     =>''
        ));

        $this->addExportType('*/*/exportSoldCsv', Mage::helper('reports')->__('CSV'));
        $this->addExportType('*/*/exportSoldExcel', Mage::helper('reports')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
