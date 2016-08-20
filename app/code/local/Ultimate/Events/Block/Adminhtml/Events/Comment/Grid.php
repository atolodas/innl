<?php
/**
 * Ultimate_Events extension
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category       Ultimate
 * @package        Ultimate_Events
 * @copyright      Copyright (c) 2014
 * @license        http://opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Events comments admin grid block
 *
 * @category    Ultimate
 * @package     Ultimate_Events
 * @author      Ultimate Module Creator
 */
class Ultimate_Events_Block_Adminhtml_Events_Comment_Grid
    extends Mage_Adminhtml_Block_Widget_Grid {
    /**
     * constructor
     * @access public
     * @author Ultimate Module Creator
     */
    public function __construct(){
        parent::__construct();
        $this->setId('eventsCommentGrid');
        $this->setDefaultSort('ct_comment_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }
    /**
     * prepare collection
     * @access protected
     * @return Ultimate_Events_Block_Adminhtml_Events_Comment_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareCollection(){
        $collection = Mage::getResourceModel('ultimate_events/events_comment_events_collection');
        $collection->addStoreData();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    /**
     * prepare grid collection
     * @access protected
     * @return Ultimate_Events_Block_Adminhtml_Events_Comment_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareColumns(){
        $this->addColumn('ct_comment_id', array(
            'header'        => Mage::helper('ultimate_events')->__('Id'),
            'index'         => 'ct_comment_id',
            'type'          => 'number',
            'filter_index'  => 'ct.comment_id',
        ));
        $this->addColumn('title', array(
            'header'        => Mage::helper('ultimate_events')->__('Event title'),
            'index'         => 'title',
            'filter_index'  => 'main_table.title',
        ));
        $this->addColumn('ct_title', array(
            'header'        => Mage::helper('ultimate_events')->__('Comment Title'),
            'index'         => 'ct_title',
            'filter_index'  => 'ct.title',
        ));
        $this->addColumn('ct_name', array(
            'header'        => Mage::helper('ultimate_events')->__('Poster name'),
            'index'         => 'ct_name',
            'filter_index'  => 'ct.name',
        ));
        $this->addColumn('ct_email', array(
            'header'        => Mage::helper('ultimate_events')->__('Poster email'),
            'index'         => 'ct_email',
            'filter_index'  => 'ct.email',
        ));
        $this->addColumn('ct_status', array(
            'header'        => Mage::helper('ultimate_events')->__('Status'),
            'index'         => 'ct_status',
            'filter_index'  => 'ct.status',
            'type'          => 'options',
            'options'       => array(
                    Ultimate_Events_Model_Events_Comment::STATUS_PENDING  => Mage::helper('ultimate_events')->__('Pending'),
                    Ultimate_Events_Model_Events_Comment::STATUS_APPROVED => Mage::helper('ultimate_events')->__('Approved'),
                    Ultimate_Events_Model_Events_Comment::STATUS_REJECTED => Mage::helper('ultimate_events')->__('Rejected'),
            )
        ));
        $this->addColumn('ct_created_at', array(
            'header'        => Mage::helper('ultimate_events')->__('Created at'),
            'index'         => 'ct_created_at',
            'width'         => '120px',
            'type'          => 'datetime',
            'filter_index'  => 'ct.created_at',
        ));
        $this->addColumn('ct_updated_at', array(
            'header'        => Mage::helper('ultimate_events')->__('Updated at'),
            'index'         => 'ct_updated_at',
            'width'         => '120px',
            'type'          => 'datetime',
            'filter_index'  => 'ct.updated_at',
        ));
        if (!Mage::app()->isSingleStoreMode() && !$this->_isExport) {
            $this->addColumn('stores', array(
                'header'=> Mage::helper('ultimate_events')->__('Store Views'),
                'index' => 'stores',
                'type'  => 'store',
                'store_all' => true,
                'store_view'=> true,
                'sortable'  => false,
                'filter_condition_callback'=> array($this, '_filterStoreCondition'),
            ));
        }
        $this->addColumn('action',
            array(
                'header'=>  Mage::helper('ultimate_events')->__('Action'),
                'width' => '100',
                'type'  => 'action',
                'getter'=> 'getCtCommentId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('ultimate_events')->__('Edit'),
                        'url'   => array('base'=> '*/*/edit'),
                        'field' => 'id'
                    )
                ),
                'filter'=> false,
                'is_system'    => true,
                'sortable'  => false,
        ));
        $this->addExportType('*/*/exportCsv', Mage::helper('ultimate_events')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('ultimate_events')->__('Excel'));
        $this->addExportType('*/*/exportXml', Mage::helper('ultimate_events')->__('XML'));
        return parent::_prepareColumns();
    }
    /**
     * prepare mass action
     * @access protected
     * @return Ultimate_Events_Block_Adminhtml_Events_Grid
     * @author Ultimate Module Creator
     */
    protected function _prepareMassaction(){
        $this->setMassactionIdField('ct_comment_id');
        $this->setMassactionIdFilter('ct.comment_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('comment');
        $this->getMassactionBlock()->addItem('delete', array(
            'label'=> Mage::helper('ultimate_events')->__('Delete'),
            'url'  => $this->getUrl('*/*/massDelete'),
            'confirm'  => Mage::helper('ultimate_events')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('ultimate_events')->__('Change status'),
            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'status' => array(
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => Mage::helper('ultimate_events')->__('Status'),
                        'values' => array(
                            Ultimate_Events_Model_Events_Comment::STATUS_PENDING  => Mage::helper('ultimate_events')->__('Pending'),
                            Ultimate_Events_Model_Events_Comment::STATUS_APPROVED => Mage::helper('ultimate_events')->__('Approved'),
                            Ultimate_Events_Model_Events_Comment::STATUS_REJECTED => Mage::helper('ultimate_events')->__('Rejected'),
                        )
                )
            )
        ));
        return $this;
    }
    /**
     * get the row url
     * @access public
     * @param Ultimate_Events_Model_Events_Comment
     * @return string
     * @author Ultimate Module Creator
     */
    public function getRowUrl($row){
        return $this->getUrl('*/*/edit', array('id' => $row->getCtCommentId()));
    }
    /**
     * get the grid url
     * @access public
     * @return string
     * @author Ultimate Module Creator
     */
    public function getGridUrl(){
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }
    /**
     * filter store column
     * @access protected
     * @param Ultimate_Events_Model_Resource_Events_Comment_Collection $collection
     * @param Mage_Adminhtml_Block_Widget_Grid_Column $column
     * @return Ultimate_Events_Block_Adminhtml_Events_Comment_Grid
     * @author Ultimate Module Creator
     */
    protected function _filterStoreCondition($collection, $column){
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $collection->setStoreFilter($value);
        return $this;
    }
}
