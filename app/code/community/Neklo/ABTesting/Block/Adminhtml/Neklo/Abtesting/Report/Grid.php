<?php

class Neklo_ABTesting_Block_Adminhtml_Neklo_ABTesting_Report_Grid extends Mage_Adminhtml_Block_Report_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('report/simple_grid.phtml');
        $this->setId('gridAbTestsLog');
        $this->setCountTotals(false);
    }

    protected function _prepareCollection()
    {

        $filter = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter)) {
            $data = array();
            $filter = base64_decode($filter);
            parse_str(urldecode($filter), $data);

            if (!isset($data['report_from'])) {
                // getting all reports from 2010 year
                $date = new Zend_Date(mktime(0,0,0,1,1,2010));
                $data['report_from'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            if (!isset($data['report_to'])) {
                // getting all reports from 2010 year
                $date = new Zend_Date();
                $data['report_to'] = $date->toString($this->getLocale()->getDateFormat('short'));
            }

            $this->_setFilterValues($data);
        } else if ($filter && is_array($filter)) {
            $this->_setFilterValues($filter);
        } else if(0 !== sizeof($this->_defaultFilter)) {
            $this->_setFilterValues($this->_defaultFilter);
        }

        $from = false;
        $to = false; 

        if($this->getFilter('report_from')) $from = date('Y-m-d 00:00:00', strtotime($this->getFilter('report_from')));
        if($this->getFilter('report_to')) $to   = date('Y-m-d 23:59:59', strtotime($this->getFilter('report_to')));


        $collection = Mage::getResourceModel('neklo_abtesting/report_collection')
                        ->getCollection()
                        ->addFieldToFilter('main_table.abevent_id', array('neq' => 1))
                        ->addExpressionFieldToSelect('events_number','count(main_table.log_id)', array())
                        ;
      

        $subSelect = Mage::getModel('neklo_abtesting/abtestpresentation')->getCollection();

        $initedPresentations = Mage::getResourceModel('neklo_abtesting/report_collection')
                        ->getCollection()
                        ->addFieldToFilter('abevent_id', 1)
                        ->addExpressionFieldToSelect('inited_number','count(*)', array())
                        ;
        

        $initedPresentations->getSelect()
            ->joinLeft(
                array('temp' => $subSelect->getSelect()),
                "main_table.abtest_abpresentation_id = temp.id",
                array('*')
            );

        $initedPresentations->getSelect()->group('CONCAT(main_table.abtest_abpresentation_id, "-", main_table.abevent_id)');

       if($from) { 
            $collection->addFieldToFilter('main_table.created_at', array('gteq' => $from));
            $initedPresentations->addFieldToFilter('main_table.created_at', array('gteq' => $from));
        }
        if($to) { 
            $collection->addFieldToFilter('main_table.created_at', array('lteq' => $to));
            $initedPresentations->addFieldToFilter('main_table.created_at', array('lteq' => $to));
        }

        $collection->getSelect()
            ->joinLeft(
                array('tpl' => $subSelect->getSelect()),
                "main_table.abtest_abpresentation_id = tpl.id",
                array('*')
            )
            ->joinLeft(
                array('abtest' => Mage::getModel('neklo_abtesting/abtest')->getCollection()->getSelect()),
                "tpl.abtest_id = abtest.abtest_id",
                array('abtest_name' => 'name', 'abtest_code' => 'code')
            )
            ->joinLeft(
                array('abpresentation' => Mage::getModel('neklo_abtesting/abpresentation')->getCollection()->getSelect()),
                "tpl.abpresentation_id = abpresentation.presentation_id",
                array('abpresentation_name' => 'name', 'abpresentation_code' => 'code')
            )
            ->joinLeft(
                array('events' => Mage::getModel('neklo_abtesting/abevent')->getCollection()->getSelect()),
                "main_table.abevent_id = events.event_id",
                array('event_name' => 'name')
            )
            ->joinLeft(
                array('inited' => $initedPresentations->getSelect()),
                "main_table.abtest_abpresentation_id = inited.abtest_abpresentation_id",
                array('inited_number' => 'inited_number')
            )
            ;

        $collection->getSelect()->group('CONCAT(main_table.abtest_abpresentation_id, "-", main_table.abevent_id)');

        $this->setCollection($collection);

        return $this;
    }

    /**
     * Prepare Grid columns
     *
     * @return Neklo_ABTesting_Block_Adminhtml_Report_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('abtest_name', array(
            'header'    =>Mage::helper('reports')->__('Ab-Test Name'),
            'index'     =>'abtest_name',
        ));

        $this->addColumn('abpresentation_name', array(
            'header'    =>Mage::helper('reports')->__('Presentation Name'),
            'index'     =>'abpresentation_name',
        ));

        $this->addColumn('event_name', array(
            'header'    =>Mage::helper('reports')->__('Event Name'),
            'index'     =>'event_name',
        ));

        $this->addColumn('inited_number', array(
            'header'    =>Mage::helper('reports')->__('Inited'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'inited_number',
            'type'      =>'number'
        ));

        $this->addColumn('events_number', array(
            'header'    =>Mage::helper('reports')->__('Successful'),
            'width'     =>'120px',
            'align'     =>'right',
            'index'     =>'events_number',
            'type'      =>'number'
        ));

        $this->addColumn('rate', array(
            'header'    =>Mage::helper('reports')->__('Conversion Rate'),
            'width'     =>'120px',
            'align'     =>'right',
            'renderer' => 'neklo_abtesting/adminhtml_neklo_abtesting_report_renderer_rate',
            'index'     =>'rate',
            'type'      =>'number'
        ));

        return $this;//parent::_prepareColumns();
    }

    public function getPeriods()
    {
        return array();
    }
}
