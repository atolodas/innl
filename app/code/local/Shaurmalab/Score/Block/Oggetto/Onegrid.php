<?php

class Shaurmalab_Score_Block_Oggetto_Onegrid extends Mage_Adminhtml_Block_Widget_Grid
{

    public  $set;

    public function getSets($ids)
    {
        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('score/oggetto')->getResource()->getTypeId())
            ->addFieldToFilter('attribute_set_id',explode(',',$this->getSet()))
           ->addOrder('attribute_set_id','asc');
            // TODO: add filter by owner when needed
        return $sets;
    }

    public function getOggettos()
    {
        $oggettos = array();

        $filters = $this->getPrefilter();
        $filters = explode(',',$filters);
        $predefined = array();

        $filters = $this->getPrefilter();
        $filters = explode(',',$filters);
        $predefined = array();

        foreach($filters as $filter) {
            if($filter) {
                list($code,$value) = explode('=',$filter);
                $predefined[$code] = $value;
            }
        }

        $customer = Mage::getModel('customer/customer')->load($predefined['owner']);
        $usernames = explode(',',$customer->getData('username'));
        $openLetters = array();
        foreach($usernames as $name) {
            $arr = explode('-',$name);
            $openLetters[] = strtolower(array_pop($arr));
        }
        $allLetters = Mage::helper('lancaster')->getAttributeSetsLettert();
            $oggettos = Mage::getModel('score/oggetto')->getCollection()
                ->addAttributeToFilter('attribute_set_id',explode(',',$this->getSet()))
                ->addAttributeToFilter('owner',$predefined['owner'])
                ->addAttributeToSelect('completed')
                ;

        $rows = new Varien_Data_Collection();
        $usernames = explode(',',$customer->getData('username'));
        foreach($this->getSets($this->getSet()) as $set)  {

            $row = new Varien_Object();
            $row->setAttrSetName($set->getAttributeSetName().' ('.$allLetters[$set->getAttributeSetId()].')');

            $status = '';

            foreach($oggettos as $oggetto) {
                if($oggetto->getAttributeSetId()==$set->getAttributeSetId()) {

                    if($oggetto->getAttributeSetId()!=25 || $oggetto->getCompleted()==1) {
                        $status = 'Completed';
                    } elseif($oggetto->getAttributeSetId()==25 || $oggetto->getCompleted()!=1) {
                        $status = "Started";
                    }
                    $row->setOid($oggetto->getId());

                }
            }
            $row->setLetter($allLetters[$set->getAttributeSetId()]);

            if($status) {
                $row->setAttrSetStatus($status);
            } elseif( in_array($allLetters[$set->getAttributeSetId()],$openLetters)) { // &&  $customer->getIsActive() ) {
                $row->setAttrSetStatus('Sent');
            } else {
                $row->setAttrSetStatus('Not Completed');
            }
//            print_r($row->getData());
//            echo "<br/><br/>";


            $rows->addItem($row);
        }


        return $rows;
    }


    protected function _getOggettoCollection()
    {

        if (is_null($this->_oggettoCollection)) {
            $layer = $this->getLayer();

            $this->_oggettoCollection = $this->getOggettos();

            if(is_object($layer)) $this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

        }

        return $this->_oggettoCollection;
    }

    public function __construct()
    {
        parent::__construct();
        if(!Mage::registry('gridCounter')) {
            Mage::register('gridCounter',1);
        } else {
            $counter = Mage::registry('gridCounter');
            Mage::unregister('gridCounter');
            Mage::register('gridCounter',$counter+1);
        }
        $this->setId('objectGrid'.Mage::registry('gridCounter'));
        $this->setUseAjax(true);
        $this->setDefaultSort('title');
        $this->setSaveParametersInSession(false);

    }

    protected function _prepareCollection()
    {

        $params = $this->getRequest()->getParams();
        $collection = $this->_getOggettoCollection();

        if(!$this->getRequest()->getParam('page')) {
            $this->getRequest()->setParam('page',1);
        }
        if(!$this->getRequest()->getParam('limit')) {
           $this->getRequest()->setParam('limit',100);
        }
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    public function getAttributesForColumns() {


        $setId = $this->getSetId();
        /* @var $groups Mage_Eav_Model_Mysql4_Entity_Attribute_Group_Collection */
        $groups = Mage::getModel('eav/entity_attribute_group')
            ->getResourceCollection()
            ->setAttributeSetFilter($setId)
            ->setSortOrder()
            ->load();
        $attributes = array();


        /* @var $node Mage_Eav_Model_Entity_Attribute_Group */
        foreach ($groups as $node) {
            $nodeChildren = Mage::getResourceModel('score/oggetto_attribute_collection')
                ->setAttributeGroupFilter($node->getId())
                ->addVisibleFilter()
                // ->checkConfigurableOggettos()
                ->load();

            if ($nodeChildren->getSize() > 0) {
                foreach ($nodeChildren->getItems() as $child) {
                    /* @var $child Mage_Eav_Model_Entity_Attribute */

                    if($child->getIsVisibleOnFront()) {
                        $attributes[] = $child;
                    }
                }
            }
        }

        return $attributes;


    }

    protected function _prepareColumns()
    {


            if($this->getWithId()) {
                $this->addColumn('entity_id', array(
                    'header'    => $this->getSet().' ID',
                    'index'     => 'entity_id',
                    'width'     =>  '10%',
                    'align'     => 'center',
                    'sortable'  => false,
                    'filter' => false,
                ));

            }

            foreach(explode(',',$this->getAddColumns()) as $column) {
                if($id = Mage::helper('score/oggetto')->isRelatedAttribute($column)) {

                    $oggettos = Mage::getModel('score/oggetto')->getAvailableObjects($id);
                    $options = array();
                    foreach($oggettos as $oggetto) {
                        $options[trim($oggetto->getSchoolByRegionId(),',')] = $oggetto->getTitle().$oggetto->getName();
                    }

                    $this->addColumn($column, array(
                        'header'    => $column,
                        'index'     => 'entity_id',
                        'type'      => 'options',
                        'width'     =>  '25%',
                        'align'     => 'center',
                        'options'   => $options,
                        'sortable'  => false
                    ));
                } else
                    if($value = Mage::helper('score/oggetto')->isNumberAttribute($column)) {

                    $index = str_replace('_num','',$column);
                    $this->addColumn($index, array(
                        'header'    => Mage::helper('core')->__($column),
                        'index'     => $index,
                        'type'      => 'counter',
                        'align'     => 'center',
                        'sortable'  => false,
                        'filter'  => false
                    ));
                } elseif($id = Mage::helper('score/oggetto')->isCounterAttribute($column,$this->getSet())) {

                    $this->addColumn($column, array(
                        'header'    => Mage::helper('core')->__($column).' '.$id,
                        'index'     => $id,
                        'set'       => $this->getSet(),
                        'type'      => 'label',
                        'align'     => 'center',
                        'sortable'  => false,
                        'filter'  => false
                    ));
                } else {
                        $columnData = array(
                            'header'    => Mage::helper('core')->__($column),
                            'width'     =>  '20%',
                            'align'     => 'center',
                            'index'     => $column,
                            'sortable'  => false,
                            'filter' => false
                        );
                        if(in_array($column,array('attr_set_name','attr_set_status','questionnary_owner'))) {
                            $columnData['renderer'] =   'score/oggetto_renderer';
                        }

                        $this->addColumn($column, $columnData);
                }

            }

        $actionStyle = " width: 108px;
            border-right: 0;
            border-spacing: 0;
            padding-left: 8px !important;
            line-height: auto !important;";
        if($this->getView()) {
            $this->addColumn('view', array(
                'header'    => Mage::helper('core')->__('Action'),
                'width'     => '25%',
                'type'      => 'text',
                'class'     => 'td-action',
                'renderer'  => 'score/oggetto_renderer',
                'style'     => $actionStyle,
                'index'   => 'view',
                'filter'    => false,
                'sortable'  => false,
            ));
        }

        if($this->getAssign()) {
            $this->addColumn('assign', array(
                'header'    => Mage::helper('core')->__('Assign'),
                'width'     => '25%',
                'type'      => 'action',
                'class'     => 'td-action',
                'style'     => $actionStyle,
                'actions'   => array(
                    array(
                        'caption'   => '<nobr>'.Mage::helper('core')->__('Add to '.$this->getAssign()).'</nobr>',
                        'url'       =>  ($this->getSet()=='Customer')?array('base'=> 'score/user/assign/to/'.$this->getAssign()):array('base'=> 'score/oggetto/assign/to/'.$this->getAssign()),
                        'field'     => 'id',
                        'class'     => 'assign-btn btn btn-action',
                    )
                ),
                'getter'    => 'getId',
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',

            ));
        }

        if($this->getEdit()) {

            $this->addColumn('edit', array(
            'header'    => Mage::helper('core')->__('Manage'),
            'width'     => '40',
            'type'      => 'action',
            'class'     => 'td-action',
            'style'     => $actionStyle,
                'actions'   => array(
                array(
                    'caption'   => Mage::helper('core')->__('Edit'),
                    'url'       =>  ($this->getSet()=='Customer')?array('base'=> 'score/user/edit'.'/form_code/'.strtolower(str_replace(' ','',$this->getGroups())).'_reg'.'/filters/'.$this->getFilters()):array('base'=> 'score/oggetto/edit'), // TODO: remove hardcode
                    'field'     => 'id',
                    'class'     => 'edit-btn btn edit-user'
                )
            ),
            'getter'    => 'getId',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',

        ));
        }

        if($this->getDelete()) {
        $this->addColumn('delete', array(
            'header'    => Mage::helper('core')->__('Delete'),
            'width'     => '40',
            'type'      => 'action',
            'class'     => 'td-action',
            'style'     => $actionStyle,
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('core')->__('Delete'),
                    'url'       => ($this->getSet()=='Customer')?array('base'=> 'score/user/delete'):array('base'=> 'score/oggetto/delete'),
                    'field'     => 'id',
                    'class'     => 'delete-btn btn btn-action',
                    'onclick' => "return confirm('".Mage::helper('core')->__('Are you sure?')."')"
                )
            ),
            'getter'    => 'getId',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',

        ));
        }


        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {

       $data = array('_current'=> true);

        foreach($this->getData() as $key=>$param)  {
            if(!is_array($param) && !is_object($param) && in_array($key,array('remove_search','prefilter','filters','view_page','form_code','add_columns','editor','with_id','groups','add','edit','view','delete','assign','set'))) $data[$key] = $param;
        }
        return $this->getUrl('score/oggetto/onegrid', $data);
    }

    public function getRowUrl($row)
    {
        return '';//$this->getUrl('score/oggetto/edit', array('id'=>$row->getId()));
    }

    public function getTitle() {
        return '';
    }

    public function getAddTitle() {
//        if(Mage::app()->getRequest()->getParam('id')) {
//            return Mage::helper('core')->__('Edit').' '.(($this->getSet()=='Customer')?$this->getSet():'User');
//        } else {
        $arr = explode(',',$this->getGroups());
        if(count($arr)==1) {
            return ($this->getSet()=='Customer')?'Register New '.$arr[0]:Mage::helper('core')->__('Add').' '.$this->getSet();
        } else {
            return ($this->getSet()=='Customer')?'Register New User':Mage::helper('core')->__('Add').' '.$this->getSet();
        }
//       }
    }

    public function getAddUrl() {


        if($this->getSet()=='Customer') {
            return $this->getUrl('score/user/add').'form_code/'.$this->getGroups().'_reg/filters/'.$this->getPrefilter();
        }
        return $this->getUrl('add-'.str_replace(' ','',strtolower($this->getSet())));
    }

}
